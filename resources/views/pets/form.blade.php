<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isEdit ? 'แก้ไขสัตว์เลี้ยง' : 'เพิ่มสัตว์เลี้ยง' }} | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/date-picker.css') }}?v={{ filemtime(public_path('css/date-picker.css')) }}">
</head>
<body>
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}"><img class="brand-icon" src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt=""> Puffly House <span>Grooming</span></a>
        @include('partials.site-nav')
        @include('partials.account-menu')
    </header>
    <main class="pet-form-page">
        <a class="detail-back" href="{{ route('pets.index') }}">← กลับไปสัตว์เลี้ยงของฉัน</a>
        <section class="pet-form-card">
            <h1>{{ $isEdit ? 'แก้ไขข้อมูลสัตว์เลี้ยง' : 'เพิ่มสัตว์เลี้ยง' }}</h1>
            <p class="pet-form-intro">กรอกข้อมูลของน้องให้ครบ เพื่อใช้ประกอบการจองบริการ</p>

            <div class="pet-type-filter">
                <label for="pet_type_id">เลือกประเภทสัตว์</label>
                <select id="pet_type_id" name="pet_type_id">
                    <option value="">เลือกประเภทสัตว์</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->pet_type_id }}" @selected((string) $selectedTypeId === (string) $type->pet_type_id)>{{ $type->pet_type_name }}</option>
                    @endforeach
                </select>
            </div>
            @if ($showTypeError)<p class="pet-error">กรุณาเลือกประเภทสัตว์ก่อนเลือกสายพันธุ์</p>@endif

            <form class="pet-form" method="POST" action="{{ $isEdit ? route('pets.update', $pet->id) : route('pets.store') }}" enctype="multipart/form-data">
                @csrf @if ($isEdit) @method('PUT') @endif
                <input id="pet_type_id_hidden" type="hidden" name="pet_type_id" value="{{ old('pet_type_id', $selectedTypeId) }}">

                <label for="pet_name">ชื่อสัตว์เลี้ยง</label>
                <input id="pet_name" name="pet_name" type="text" value="{{ old('pet_name', $pet->pet_name) }}" required maxlength="100">
                @error('pet_name')<p class="pet-error">{{ $message }}</p>@enderror

                <label for="pet_breed_id">สายพันธุ์</label>
                <select id="pet_breed_id" name="pet_breed_id" required @disabled(!$selectedTypeId || $breeds->isEmpty())>
                    <option value="">{{ $selectedTypeId ? 'เลือกสายพันธุ์' : 'เลือกประเภทสัตว์ก่อน' }}</option>
                    @foreach ($breeds as $breed)
                        <option value="{{ $breed->pet_breed_id }}" @selected((string) old('pet_breed_id', $pet->pet_breed_id) === (string) $breed->pet_breed_id)>{{ $breed->pet_breed_name }}</option>
                    @endforeach
                </select>
                <p id="pet-breed-help" class="pet-type-help">@if ($selectedTypeId && $breeds->isEmpty())ไม่พบสายพันธุ์ของประเภทที่เลือก @elseif (!$selectedTypeId)เลือกประเภทสัตว์ก่อนเลือกสายพันธุ์ @elseเลือกสายพันธุ์ของสัตว์เลี้ยง@endif</p>
                @error('pet_breed_id')<p class="pet-error">กรุณาเลือกสายพันธุ์ของประเภทสัตว์ที่เลือก</p>@enderror
                @error('pet_type_id')<p class="pet-error">{{ $message }}</p>@enderror

                @if ($pet->pet_image)
                    <div class="pet-form-image-preview"><span>รูปปัจจุบัน</span><img src="{{ asset('storage/'.$pet->pet_image) }}" alt="รูปสัตว์เลี้ยง {{ $pet->pet_name }}"></div>
                @endif
                <label for="pet_image">รูปสัตว์เลี้ยง <span class="optional">(ไม่บังคับ, JPG/PNG/WEBP ไม่เกิน 5 MB){{ $pet->pet_image ? ' · เลือกรูปใหม่เพื่อเปลี่ยนรูปเดิม' : '' }}</span></label>
                <input id="pet_image" name="pet_image" type="file" accept="image/jpeg,image/png,image/webp">
                @error('pet_image')<p class="pet-error">{{ $message }}</p>@enderror

                <label for="pet_birth">วันเกิด</label>
                <input id="pet_birth" name="pet_birth" type="date" value="{{ old('pet_birth', $pet->pet_birth?->format('Y-m-d')) }}" required>
                @error('pet_birth')<p class="pet-error">{{ $message }}</p> @enderror

                <div class="pet-birth-unknown">
                    <input type="checkbox" id="pet_birth_unknown" name="pet_birth_unknown" value="1" @checked(old('pet_birth_unknown', is_null($pet->pet_birth)))>
                    <label for="pet_birth_unknown">ไม่ทราบวันเกิด</label>
                </div>

                <div class="pet-form-grid">
                    <div><label for="pet_gender">เพศ</label><select id="pet_gender" name="pet_gender" required><option value="">เลือกเพศ</option><option value="male" @selected(old('pet_gender', $pet->pet_gender) === 'male')>ตัวผู้</option><option value="female" @selected(old('pet_gender', $pet->pet_gender) === 'female')>ตัวเมีย</option></select>@error('pet_gender')<p class="pet-error">{{ $message }}</p>@enderror</div>
                    <div><label for="pet_weight">น้ำหนัก (กก.)</label><input id="pet_weight" name="pet_weight" type="number" min="0.01" step="0.01" value="{{ old('pet_weight', $pet->pet_weight) }}" required>@error('pet_weight')<p class="pet-error">{{ $message }}</p>@enderror</div>
                </div>

                <textarea id="pet_notes" name="pet_notes" rows="3">{{ old('pet_notes', $pet->pet_notes) }}</textarea>
                @error('pet_notes')<p class="pet-error">{{ $message }}</p>@enderror

                <div class="pet-form-actions"><a class="pet-cancel" href="{{ route('pets.index') }}">ยกเลิก</a><button class="button" type="submit">{{ $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกข้อมูลสัตว์เลี้ยง' }}</button></div>
            </form>
        </section>
    </main>
<script>
    const petTypes = document.getElementById('pet_type_id');
    const petTypeHidden = document.getElementById('pet_type_id_hidden');
    const petBreeds = document.getElementById('pet_breed_id');
    const petBreedHelp = document.getElementById('pet-breed-help');
    const allPetBreeds = @json($allBreeds->map(fn ($breed) => ['id' => $breed->pet_breed_id, 'typeId' => $breed->pet_type_id, 'name' => $breed->pet_breed_name])->values());

    petTypes.addEventListener('change', () => {
        const selectedTypeId = petTypes.value;
        const previousBreedId = petBreeds.value;
        const matchingBreeds = allPetBreeds.filter((breed) => String(breed.typeId) === selectedTypeId);

        petTypeHidden.value = selectedTypeId;
        petBreeds.replaceChildren(new Option(selectedTypeId ? 'เลือกสายพันธุ์' : 'เลือกประเภทสัตว์ก่อน', ''));
        matchingBreeds.forEach((breed) => {
            const option = new Option(breed.name, breed.id);
            option.selected = String(breed.id) === previousBreedId;
            petBreeds.add(option);
        });
        petBreeds.disabled = !selectedTypeId || matchingBreeds.length === 0;
        petBreedHelp.textContent = !selectedTypeId
            ? 'เลือกประเภทสัตว์ก่อนเลือกสายพันธุ์'
            : matchingBreeds.length === 0
                ? 'ไม่พบสายพันธุ์ของประเภทที่เลือก'
                : 'เลือกสายพันธุ์ของสัตว์เลี้ยง';
        petBreedHelp.classList.toggle('pet-error', Boolean(selectedTypeId) && matchingBreeds.length === 0);
        petBreedHelp.classList.toggle('pet-type-help', !selectedTypeId || matchingBreeds.length > 0);
    });

    const birthInput = document.getElementById('pet_birth');
    const birthUnknown = document.getElementById('pet_birth_unknown');
    const syncBirthInput = () => {
        birthInput.required = !birthUnknown.checked;
        birthInput.disabled = birthUnknown.checked;
        if (birthUnknown.checked) birthInput.value = '';
    };
    birthUnknown.addEventListener('change', syncBirthInput);
    syncBirthInput();
</script>
<script src="{{ asset('js/date-picker.js') }}?v={{ filemtime(public_path('js/date-picker.js')) }}"></script>
</body>
</html>
