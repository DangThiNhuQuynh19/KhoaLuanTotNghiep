<div id="permissions" class="content-transition lg:ml-64 pt-16 min-h-screen">
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Phân quyền hệ thống</h2>
                        <p class="text-gray-600">Quản lý vai trò và quyền hạn người dùng</p>
                    </div>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tạo vai trò mới
                    </button>
                </div>
            </div>

            <!-- Roles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-red-100 p-3 rounded-lg">
                            <i class="fas fa-crown text-red-600 text-xl"></i>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Super Admin</h3>
                    <p class="text-gray-600 mb-4">Quyền truy cập toàn bộ hệ thống</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">2 người dùng</span>
                        <button class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Chỉnh sửa</button>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Admin</h3>
                    <p class="text-gray-600 mb-4">Quản lý nội dung và người dùng</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">5 người dùng</span>
                        <button class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Chỉnh sửa</button>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-edit text-green-600 text-xl"></i>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Editor</h3>
                    <p class="text-gray-600 mb-4">Tạo và chỉnh sửa nội dung</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">12 người dùng</span>
                        <button class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Chỉnh sửa</button>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <i class="fas fa-eye text-purple-600 text-xl"></i>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Viewer</h3>
                    <p class="text-gray-600 mb-4">Chỉ xem nội dung</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">37 người dùng</span>
                        <button class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Chỉnh sửa</button>
                    </div>
                </div>
            </div>

            <!-- Permissions Matrix -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Ma trận phân quyền</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left py-4 px-6 font-semibold text-gray-800">Chức năng</th>
                                <th class="text-center py-4 px-6 font-semibold text-gray-800">Super Admin</th>
                                <th class="text-center py-4 px-6 font-semibold text-gray-800">Admin</th>
                                <th class="text-center py-4 px-6 font-semibold text-gray-800">Editor</th>
                                <th class="text-center py-4 px-6 font-semibold text-gray-800">Viewer</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="py-4 px-6 font-medium text-gray-800">Quản lý bài viết</td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-check text-green-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-check text-green-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-check text-green-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-times text-red-600"></i>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 px-6 font-medium text-gray-800">Quản lý nhân sự</td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-check text-green-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-check text-green-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-times text-red-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-times text-red-600"></i>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 px-6 font-medium text-gray-800">Phân quyền</td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-check text-green-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-times text-red-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-times text-red-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-times text-red-600"></i>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 px-6 font-medium text-gray-800">Xem báo cáo</td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-check text-green-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-check text-green-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-check text-green-600"></i>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <i class="fas fa-check text-green-600"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </main>
</body>
</html>