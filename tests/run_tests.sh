#!/bin/bash
# Script để chạy test cases cho chức năng đăng ký

echo "=========================================="
echo "  Chạy Test Cases - Chức Năng Đăng Ký"
echo "=========================================="
echo ""

# Kiểm tra PHP có tồn tại không
if ! command -v php &> /dev/null; then
    echo "Error: PHP chưa được cài đặt"
    exit 1
fi

# Hiển thị phiên bản PHP
echo "PHP Version: $(php -v | head -n 1)"
echo ""

# Chạy test
echo "Đang chạy test cases..."
echo ""

cd "$(dirname "$0")/.."
php tests/RegistrationTest.php

# Lấy exit code
exit_code=$?

echo ""
if [ $exit_code -eq 0 ]; then
    echo "✓ Tất cả test cases đã hoàn thành!"
else
    echo "✗ Có test cases thất bại!"
fi

exit $exit_code
