<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isEdit ? 'แก้ไขสัตว์เลี้ยง' : 'เพิ่มสัตว์เลี้ยง' }} | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body>
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}"><span class="brand-icon">🐾</span> Puffly House <span>Grooming</span></a>
        @include('partials.site-nav')
        @include('partials.account-menu')
    </header>
    <main class="pet-form-page">
        <a class="detail-back" href="{{ route('pets.index') }}">← กลับไปสัตว์เลี้ยงของฉัน</a>
        <section class="pet-form-card">
            <h1>{{ $isEdit ? 'แก้ไขข้อมูลสัตว์เลี้ยง' : 'เพิ่มสัตว์เลี้ยง' }}</h1>
            <p class="pet-form-intro">กรอกข้อมูลของน้องให้ครบ เพื่อใช้ประกอบการจองบริการ</p>

            <form class="pet-type-filter" method="GET" action="{{ $isEdit ? route('pets.edit', $pet->id) : route('pets.create') }}">
                <label for="pet_type_id">เลือกประเภทสัตว์</label>
                <select id="pet_type_id" name="pet_type_id">
                    <option value="">เลือกประเภทสัตว์</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->pet_type_id }}" @selected((string) $selectedTypeId === (string) $type->pet_type_id)>{{ $type->pet_type_name }}</option>
                    @endforeach
                </select>
                <button class="button button-small" type="submit">แสดงสายพันธุ์</button>
            </form>
            @if ($showTypeError)<p class="pet-error">กรุณาเลือกประเภทสัตว์ก่อนแสดงสายพันธุ์</p>@endif

            <form class="pet-form" method="POST" action="{{ $isEdit ? route('pets.update', $pet->id) : route('pets.store') }}">
                @csrf @if ($isEdit) @method('PUT') @endif
                <input type="hidden" name="pet_type_id" value="{{ old('pet_type_id', $selectedTypeId) }}">

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
                @if ($selectedTypeId && $breeds->isEmpty())<p class="pet-error">ไม่พบสายพันธุ์ของประเภทที่เลือก</p>@elseif (!$selectedTypeId)<p class="pet-type-help">เลือกประเภทสัตว์และกด “แสดงสายพันธุ์” ก่อนเลือกสายพันธุ์</p>@endif
                @error('pet_breed_id')<p class="pet-error">กรุณาเลือกสายพันธุ์ของประเภทสัตว์ที่เลือก</p>@enderror
                @error('pet_type_id')<p class="pet-error">{{ $message }}</p>@enderror

                <label for="pet_birth">วันเกิด</label>
                <input id="pet_birth" name="pet_birth" type="date" value="{{ old('pet_birth', $pet->pet_birth?->format('Y-m-d')) }}" required>
                @error('pet_birth')<p class="pet-error">{{ $message }}</p>@enderror

                <div class="pet-form-grid">
                    <div><label for="pet_gender">เพศ</label><select id="pet_gender" name="pet_gender" required><option value="">เลือกเพศ</option><option value="male" @selected(old('pet_gender', $pet->pet_gender) === 'male')>ตัวผู้</option><option value="female" @selected(old('pet_gender', $pet->pet_gender) === 'female')>ตัวเมีย</option></select>@error('pet_gender')<p class="pet-error">{{ $message }}</p>@enderror</div>
                    <div><label for="pet_weight">น้ำหนัก (กก.)</label><input id="pet_weight" name="pet_weight" type="number" min="0.01" step="0.01" value="{{ old('pet_weight', $pet->pet_weight) }}" required>@error('pet_weight')<p class="pet-error">{{ $message }}</p>@enderror</div>
                </div>

                <label for="pet_notes">หมายเหตุ <span class="optional">(ไม่บังคับ)</span></label>
                <textarea id="pet_notes" name="pet_notes" rows="3">{{ old('pet_notes', $pet->pet_notes) }}</textarea>
                @error('pet_notes')<p class="pet-error">{{ $message }}</p>@enderror

                <div class="pet-form-actions"><a class="pet-cancel" href="{{ route('pets.index') }}">ยกเลิก</a><button class="button" type="submit">{{ $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกข้อมูลสัตว์เลี้ยง' }}</button></div>
            </form>
        </section>
    </main>
</body>
</html>
