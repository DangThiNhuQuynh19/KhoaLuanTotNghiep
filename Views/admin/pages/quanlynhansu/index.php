<!-- Staff Management Page -->
<div id="staff" class="content-transition lg:ml-64 pt-16 min-h-screen">
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Quản lý nhân sự</h2>
                        <p class="text-gray-600">Quản lý thông tin và quyền hạn nhân viên</p>
                    </div>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>Thêm nhân viên
                    </button>
                </div>
            </div>

            <!-- Staff Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Tổng nhân viên</p>
                            <p class="text-2xl font-bold text-gray-800">56</p>
                        </div>
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Đang hoạt động</p>
                            <p class="text-2xl font-bold text-green-600">48</p>
                        </div>
                        <i class="fas fa-user-check text-green-600 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Nghỉ phép</p>
                            <p class="text-2xl font-bold text-orange-600">8</p>
                        </div>
                        <i class="fas fa-user-clock text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Staff Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-wrap gap-4">
                        <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option>Tất cả phòng ban</option>
                            <option>IT</option>
                            <option>Marketing</option>
                            <option>HR</option>
                        </select>
                        <input type="text" placeholder="Tìm kiếm nhân viên..." class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 flex-1 min-w-64">
                    </div>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Nhân viên</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Chức vụ</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Phòng ban</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Trạng thái</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Ngày vào</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-800">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%234F46E5'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-size='14' font-weight='bold'%3ENA%3C/text%3E%3C/svg%3E" alt="Avatar" class="w-10 h-10 rounded-full">
                                    <div>
                                        <div class="font-medium text-gray-800">Nguyễn Văn A</div>
                                        <div class="text-sm text-gray-600">nguyenvana@company.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-gray-600">Senior Developer</td>
                            <td class="py-4 px-6">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">IT</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Hoạt động</span>
                            </td>
                            <td class="py-4 px-6 text-gray-600">01/03/2023</td>
                            <td class="py-4 px-6">
                                <div class="flex space-x-2">
                                    <button class="text-indigo-600 hover:text-indigo-800 p-2 rounded-lg hover:bg-indigo-50">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50">
                                        <i class="fas fa-user-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%23EC4899'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-size='14' font-weight='bold'%3ETB%3C/text%3E%3C/svg%3E" alt="Avatar" class="w-10 h-10 rounded-full">
                                    <div>
                                        <div class="font-medium text-gray-800">Trần Thị B</div>
                                        <div class="text-sm text-gray-600">tranthib@company.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-gray-600">Marketing Manager</td>
                            <td class="py-4 px-6">
                                <span class="bg-pink-100 text-pink-800 px-3 py-1 rounded-full text-sm font-medium">Marketing</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium">Nghỉ phép</span>
                            </td>
                            <td class="py-4 px-6 text-gray-600">15/06/2023</td>
                            <td class="py-4 px-6">
                                <div class="flex space-x-2">
                                    <button class="text-indigo-600 hover:text-indigo-800 p-2 rounded-lg hover:bg-indigo-50">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50">
                                        <i class="fas fa-user-times"></i>
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