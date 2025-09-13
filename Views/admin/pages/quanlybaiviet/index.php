 <!-- Posts Management Page -->
 <div id="posts" class="content-transition lg:ml-64 pt-16 min-h-screen">
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Quản lý bài viết</h2>
                        <p class="text-gray-600">Tạo, chỉnh sửa và quản lý nội dung</p>
                    </div>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tạo bài viết mới
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex flex-wrap gap-4">
                    <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>Tất cả trạng thái</option>
                        <option>Đã xuất bản</option>
                        <option>Nháp</option>
                        <option>Chờ duyệt</option>
                    </select>
                    <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>Tất cả danh mục</option>
                        <option>Tin tức</option>
                        <option>Hướng dẫn</option>
                        <option>Thông báo</option>
                    </select>
                    <input type="text" placeholder="Tìm kiếm bài viết..." class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 flex-1 min-w-64">
                </div>
            </div>

            <!-- Posts Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Tiêu đề</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Tác giả</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Danh mục</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Trạng thái</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Ngày tạo</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div class="font-medium text-gray-800">Hướng dẫn sử dụng hệ thống mới</div>
                                <div class="text-sm text-gray-600">Cập nhật các tính năng mới nhất...</div>
                            </td>
                            <td class="py-4 px-6 text-gray-600">Nguyễn Văn A</td>
                            <td class="py-4 px-6">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">Hướng dẫn</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Đã xuất bản</span>
                            </td>
                            <td class="py-4 px-6 text-gray-600">15/12/2024</td>
                            <td class="py-4 px-6">
                                <div class="flex space-x-2">
                                    <button class="text-indigo-600 hover:text-indigo-800 p-2 rounded-lg hover:bg-indigo-50">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div class="font-medium text-gray-800">Thông báo bảo trì hệ thống</div>
                                <div class="text-sm text-gray-600">Lịch bảo trì định kỳ tháng 12...</div>
                            </td>
                            <td class="py-4 px-6 text-gray-600">Trần Thị B</td>
                            <td class="py-4 px-6">
                                <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium">Thông báo</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">Chờ duyệt</span>
                            </td>
                            <td class="py-4 px-6 text-gray-600">14/12/2024</td>
                            <td class="py-4 px-6">
                                <div class="flex space-x-2">
                                    <button class="text-indigo-600 hover:text-indigo-800 p-2 rounded-lg hover:bg-indigo-50">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </main>
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>
</body>
</html>