<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เพิ่มสัตว์เลี้ยง | Puffly House Grooming</title>
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
        <a class="detail-back" href="{{ route('pets.create') }}">← กลับไปเลือกประเภท</a>
        <section class="pet-form-card">
            <h1>เพิ่มสัตว์เลี้ยง</h1>
            <p class="pet-form-intro">กรอกข้อมูลของน้องให้ครบ เพื่อใช้ประกอบการจองบริการ</p>
            <div class="selected-pet-type">ประเภทสัตว์: <strong>{{ $selectedType->pet_type_name }}</strong></div>

            <form class="pet-form" method="POST" action="{{ route('pets.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="pet_type_id" value="{{ $selectedType->pet_type_id }}">

                <label for="pet_name">ชื่อสัตว์เลี้ยง</label>
                <input id="pet_name" name="pet_name" type="text" value="{{ old('pet_name') }}" required maxlength="100">
                @error('pet_name')<p class="pet-error">{{ $message }}</p>@enderror

                <label for="pet_breed_id">สายพันธุ์</label>
                <select id="pet_breed_id" name="pet_breed_id" required>
                    <option value="">เลือกสายพันธุ์</option>
                    @foreach ($breeds as $breed)
                        <option value="{{ $breed->pet_breed_id }}" @selected((string) old('pet_breed_id') === (string) $breed->pet_breed_id)>{{ $breed->pet_breed_name }}</option>
                    @endforeach
                </select>
                @if ($breeds->isEmpty())<p class="pet-error">ไม่พบสายพันธุ์ของประเภทที่เลือก</p>@endif
                @error('pet_breed_id')<p class="pet-error">กรุณาเลือกสายพันธุ์</p>@enderror

                <label for="pet_image">รูปสัตว์เลี้ยง <span class="optional">(ไม่บังคับ, JPG/PNG/WEBP ไม่เกิน 5 MB)</span></label>
                <input id="pet_image" name="pet_image" type="file" accept="image/jpeg,image/png,image/webp">
                @error('pet_image')<p class="pet-error">{{ $message }}</p>@enderror

                <label for="pet_birth">วันเกิด</label>
                <input id="pet_birth" name="pet_birth" type="date" value="{{ old('pet_birth') }}" required>
                @error('pet_birth')<p class="pet-error">{{ $message }}</p>@enderror
                <div class="pet-birth-unknown"><input type="checkbox" id="pet_birth_unknown" name="pet_birth_unknown" value="1" @checked(old('pet_birth_unknown'))><label for="pet_birth_unknown">ไม่ทราบวันเกิด</label></div>

                <div class="pet-form-grid">
                    <div><label for="pet_gender">เพศ</label><select id="pet_gender" name="pet_gender" required><option value="">เลือกเพศ</option><option value="male" @selected(old('pet_gender') === 'male')>ตัวผู้</option><option value="female" @selected(old('pet_gender') === 'female')>ตัวเมีย</option></select>@error('pet_gender')<p class="pet-error">{{ $message }}</p>@enderror</div>
                    <div><label for="pet_weight">น้ำหนัก (กก.)</label><input id="pet_weight" name="pet_weight" type="number" min="0.01" step="0.01" value="{{ old('pet_weight') }}" required>@error('pet_weight')<p class="pet-error">{{ $message }}</p>@enderror</div>
                </div>

                <label for="pet_notes">หมายเหตุ <span class="optional">(ไม่บังคับ)</span></label>
                <textarea id="pet_notes" name="pet_notes" rows="3">{{ old('pet_notes') }}</textarea>
                @error('pet_notes')<p class="pet-error">{{ $message }}</p>@enderror

                <div class="pet-form-actions"><a class="pet-cancel" href="{{ route('pets.index') }}">ยกเลิก</a><button class="button" type="submit">บันทึกข้อมูลสัตว์เลี้ยง</button></div>
            </form>
        </section>
    </main>
<script>
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
