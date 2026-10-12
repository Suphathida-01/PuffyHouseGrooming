(() => {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const formatValue = (date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    const parseValue = (value) => {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) return null;
        const [year, month, day] = value.split('-').map(Number);
        return new Date(year, month - 1, day);
    };
    const today = () => {
        const now = new Date();
        return new Date(now.getFullYear(), now.getMonth(), now.getDate());
    };

    dateInputs.forEach((input) => {
        const widget = document.createElement('span');
        widget.className = 'date-picker-widget';
        input.parentNode.insertBefore(widget, input);
        widget.append(input);
        input.classList.add('date-picker-source');
        input.dataset.datePicker = 'true';
        input.dataset.dateRequired = input.required ? 'true' : 'false';
        input.required = false;

        const textInput = document.createElement('input');
        textInput.type = 'text';
        textInput.className = 'date-picker-text';
        textInput.placeholder = 'mm/dd/yyyy';
        textInput.inputMode = 'numeric';
        textInput.autocomplete = 'off';
        textInput.setAttribute('aria-label', input.getAttribute('aria-label') || 'Enter date as month/day/year');
        if (input.id) {
            textInput.id = `${input.id}-text`;
            input.labels?.forEach((label) => {
                if (label.htmlFor === input.id) label.htmlFor = textInput.id;
            });
        }
        widget.append(textInput);

        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'date-picker-trigger';
        trigger.setAttribute('aria-haspopup', 'dialog');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.setAttribute('aria-label', input.getAttribute('aria-label') || input.labels?.[0]?.textContent?.trim() || 'Choose date');
        if (input.id) {
            trigger.id = `${input.id}-picker`;
        }
        widget.append(trigger);

        const popover = document.createElement('div');
        popover.className = 'date-picker-popover';
        popover.setAttribute('role', 'dialog');
        popover.setAttribute('aria-label', 'Choose date');
        popover.hidden = true;
        popover.innerHTML = '<div class="date-picker-heading"><button type="button" data-date-prev aria-label="Previous month">‹</button><strong data-date-month></strong><button type="button" data-date-next aria-label="Next month">›</button></div><div class="date-picker-weekdays" aria-hidden="true"><span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span></div><div class="date-picker-days" role="grid"></div><div class="date-picker-footer"><button type="button" data-date-clear>Clear</button><button type="button" data-date-today>Today</button></div>';
        document.body.append(popover);

        const monthLabel = popover.querySelector('[data-date-month]');
        const daysGrid = popover.querySelector('.date-picker-days');
        let visibleMonth;

        const syncTrigger = () => {
            const date = parseValue(input.value);
            textInput.value = date
                ? `${String(date.getMonth() + 1).padStart(2, '0')}/${String(date.getDate()).padStart(2, '0')}/${date.getFullYear()}`
                : '';
            trigger.disabled = input.disabled;
            textInput.disabled = input.disabled;
            textInput.classList.toggle('is-invalid', input.dataset.dateRequired === 'true' && !input.value);
        };

        const applyTypedDate = () => {
            const match = textInput.value.trim().match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
            if (!match) {
                textInput.classList.toggle('is-invalid', textInput.value.trim() !== '' || input.dataset.dateRequired === 'true');
                return false;
            }
            const [, month, day, year] = match;
            const date = new Date(Number(year), Number(month) - 1, Number(day));
            if (date.getFullYear() !== Number(year) || date.getMonth() !== Number(month) - 1 || date.getDate() !== Number(day)) {
                textInput.classList.add('is-invalid');
                return false;
            }
            input.value = formatValue(date);
            input.dispatchEvent(new Event('change', { bubbles: true }));
            syncTrigger();
            return true;
        };

        const positionPopover = () => {
            const rect = trigger.getBoundingClientRect();
            const width = 252;
            const height = popover.offsetHeight;
            const left = Math.max(8, Math.min(rect.left, window.innerWidth - width - 8));
            const below = rect.bottom + 7;
            const top = below + height <= window.innerHeight - 8 ? below : Math.max(8, rect.top - height - 7);
            popover.style.left = `${left}px`;
            popover.style.top = `${top}px`;
        };

        const renderCalendar = () => {
            const year = visibleMonth.getFullYear();
            const month = visibleMonth.getMonth();
            monthLabel.textContent = visibleMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            daysGrid.replaceChildren();
            const firstWeekday = new Date(year, month, 1).getDay();
            const gridStart = new Date(year, month, 1 - firstWeekday);
            const selectedDate = input.value;
            const currentDate = formatValue(today());

            for (let index = 0; index < 42; index++) {
                const date = new Date(gridStart.getFullYear(), gridStart.getMonth(), gridStart.getDate() + index);
                const dateValue = formatValue(date);
                const dayButton = document.createElement('button');
                dayButton.type = 'button';
                dayButton.className = 'date-picker-day';
                dayButton.textContent = String(date.getDate());
                dayButton.setAttribute('role', 'gridcell');
                if (date.getMonth() !== month) dayButton.classList.add('is-outside-month');
                if (dateValue === selectedDate) dayButton.classList.add('is-selected');
                if (dateValue === currentDate) dayButton.classList.add('is-today');
                dayButton.addEventListener('click', () => {
                    input.value = dateValue;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    syncTrigger();
                    closePopover();
                    if (input.form?.hasAttribute('data-date-picker-auto-submit')) {
                        input.form.requestSubmit();
                    }
                });
                daysGrid.append(dayButton);
            }
            positionPopover();
        };

        const closePopover = () => {
            popover.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
        };

        const openPopover = () => {
            if (input.disabled) return;
            const selectedDate = parseValue(input.value) || today();
            visibleMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
            popover.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            renderCalendar();
        };

        trigger.addEventListener('click', () => popover.hidden ? openPopover() : closePopover());
        popover.querySelector('[data-date-prev]').addEventListener('click', () => {
            visibleMonth.setMonth(visibleMonth.getMonth() - 1);
            renderCalendar();
        });
        popover.querySelector('[data-date-next]').addEventListener('click', () => {
            visibleMonth.setMonth(visibleMonth.getMonth() + 1);
            renderCalendar();
        });
        popover.querySelector('[data-date-clear]').addEventListener('click', () => {
            input.value = '';
            input.dispatchEvent(new Event('change', { bubbles: true }));
            syncTrigger();
            closePopover();
            if (input.form?.hasAttribute('data-date-picker-auto-submit')) input.form.requestSubmit();
        });
        popover.querySelector('[data-date-today]').addEventListener('click', () => {
            const date = today();
            input.value = formatValue(date);
            input.dispatchEvent(new Event('change', { bubbles: true }));
            syncTrigger();
            closePopover();
            if (input.form?.hasAttribute('data-date-picker-auto-submit')) input.form.requestSubmit();
        });
        input.addEventListener('change', syncTrigger);
        textInput.addEventListener('change', () => {
            if (applyTypedDate() && input.form?.hasAttribute('data-date-picker-auto-submit')) input.form.requestSubmit();
        });
        textInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                if (applyTypedDate()) input.form?.requestSubmit();
            }
        });
        input.addEventListener('requiredchange', syncTrigger);
        input.addEventListener('disabledchange', syncTrigger);
        input.form?.addEventListener('submit', (event) => {
            if (input.dataset.dateRequired === 'true' && !input.disabled && !input.value) {
                event.preventDefault();
                openPopover();
                textInput.classList.add('is-invalid');
                textInput.focus();
            }
        });
        input.addEventListener('change', () => textInput.classList.remove('is-invalid'));
        textInput.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown' && popover.hidden) {
                event.preventDefault();
                openPopover();
            }
        });

        document.addEventListener('click', (event) => {
            if (!widget.contains(event.target) && !popover.contains(event.target)) closePopover();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !popover.hidden) {
                closePopover();
                trigger.focus();
            }
        });
        window.addEventListener('resize', () => { if (!popover.hidden) positionPopover(); });
        window.addEventListener('scroll', () => { if (!popover.hidden) positionPopover(); }, true);
        new MutationObserver(() => {
            input.dataset.dateRequired = input.required ? 'true' : 'false';
            syncTrigger();
        }).observe(input, { attributes: true, attributeFilter: ['disabled', 'required'] });
        syncTrigger();
    });
})();
