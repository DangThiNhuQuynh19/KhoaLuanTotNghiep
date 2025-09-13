    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-16 h-full w-64 bg-white shadow-lg border-r border-gray-200 sidebar-transition z-40 transform -translate-x-full lg:translate-x-0">
        <nav class="p-6">
            <ul class="space-y-2">
                <li>
                    <button onclick="showPage('dashboard')" class="nav-item w-full flex items-center space-x-3 px-4 py-3 text-left rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-colors active">
                        <i class="fas fa-home text-lg"></i>
                        <a href="?action=trangchu" class="font-medium">Trang chủ</a>
                    </button>
                </li>
                <li>
                    <button onclick="showPage('posts')" class="nav-item w-full flex items-center space-x-3 px-4 py-3 text-left rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-newspaper text-lg"></i>
                        <a href="?action=quanlybaiviet" class="font-medium">Quản lý bài viết</a>
                    </button>
                </li>
                <li>
                    <button onclick="showPage('staff')" class="nav-item w-full flex items-center space-x-3 px-4 py-3 text-left rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-users text-lg"></i>
                        <a href="?action=quanlynhansu" class="font-medium">Quản lý nhân sự</a>
                    </button>
                </li>
                <li>
                    <button onclick="showPage('permissions')" class="nav-item w-full flex items-center space-x-3 px-4 py-3 text-left rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-shield-alt text-lg"></i>
                        <a href="?action=phanquyen" class="font-medium">Phân quyền</a>
                    </button>
                </li>
                <li class="pt-4 border-t border-gray-200">
                    <button class="w-full flex items-center space-x-3 px-4 py-3 text-left rounded-lg hover:bg-red-50 hover:text-red-600 transition-colors text-gray-600">
                        <i class="fas fa-sign-out-alt text-lg"></i>
                        <span class="font-medium">Đăng xuất</span>
                    </button>
                </li>
            </ul>
        </nav>
    </aside>