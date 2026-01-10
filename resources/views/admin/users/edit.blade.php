@extends('layouts.admin')
@section('title','Cập nhật vai trò')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">← Quay lại</a>
            </div>

            <h5 class="mb-3">Tài khoản: <strong>{{ $user->name }}</strong> <span class="text-muted">({{ $user->email }})</span></h5>

            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="roleForm" method="POST" action="{{ route('admin.users.update', $user) }}" class="row g-3">
                @csrf
                @method('PUT')

                {{-- Server-side require confirmation when role is changed --}}
                <input type="hidden" name="confirm_role_change" id="confirmRoleChange" value="0">

                <div class="col-md-6">
                    <label class="form-label form-required">Vai trò</label>
                    <select id="roleSelect" class="form-select" name="role" required>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" @selected(old('role', $user->role) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hiện tại bạn chỉ cần 3 role: Admin, Khách hàng, Nhân viên (chưa cần chức năng phân quyền).</small>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-success">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const form = document.getElementById('roleForm');
        const select = document.getElementById('roleSelect');
        const confirmInput = document.getElementById('confirmRoleChange');

        // Blade values
        const currentRole = @json($user->role);
        const isSelf = @json(auth()->id() === $user->id);
        const ROLE_ADMIN = 'admin';
        const roles = @json($roles);

        if (!form || !select) return;

        form.addEventListener('submit', function (e) {
            const nextRole = select.value;

            // Reset
            if (confirmInput) confirmInput.value = '0';

            // Không đổi role => không cần confirm
            if (nextRole === currentRole) return;

            const fromLabel = roles?.[currentRole] ?? currentRole;
            const toLabel = roles?.[nextRole] ?? nextRole;

            const isDowngradeFromAdmin = (currentRole === ROLE_ADMIN && nextRole !== ROLE_ADMIN);
            const msg = isDowngradeFromAdmin
                ? (isSelf
                    ? `Bạn đang tự HẠ quyền Admin của chính mình (từ "${fromLabel}" → "${toLabel}"). Sau khi lưu, bạn có thể mất quyền truy cập trang quản trị và sẽ bị đăng xuất. Bạn chắc chắn muốn tiếp tục?`
                    : `Bạn đang HẠ quyền Admin của tài khoản này (từ "${fromLabel}" → "${toLabel}"). Tài khoản sẽ mất quyền truy cập tính năng quản trị. Bạn chắc chắn muốn tiếp tục?`)
                : (isSelf
                    ? `Bạn đang thay đổi vai trò của chính mình (từ "${fromLabel}" → "${toLabel}"). Bạn chắc chắn muốn tiếp tục?`
                    : `Bạn đang thay đổi vai trò tài khoản này (từ "${fromLabel}" → "${toLabel}"). Bạn chắc chắn muốn tiếp tục?`);

            if (!confirm(msg)) {
                e.preventDefault();
                return;
            }

            // Mark confirmed for backend validation
            if (confirmInput) confirmInput.value = '1';
        });
    })();
</script>
@endsection
