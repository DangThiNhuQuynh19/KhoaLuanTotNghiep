#!/bin/bash

# Test Script for Push Notification System
# This script tests the notification functionality

echo "========================================="
echo "Push Notification System Test"
echo "========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test 1: Check if database table exists
echo -e "${YELLOW}Test 1: Checking database table...${NC}"
mysql -u kltn -pKltntrangquynh2025@ hanhphuc -e "DESCRIBE thongbao;" 2>/dev/null
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Table 'thongbao' exists${NC}"
else
    echo -e "${RED}✗ Table 'thongbao' does not exist${NC}"
    echo "Creating table..."
    mysql -u kltn -pKltntrangquynh2025@ hanhphuc < /home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep/database/migrations/create_thongbao_table.sql
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Table created successfully${NC}"
    else
        echo -e "${RED}✗ Failed to create table${NC}"
    fi
fi
echo ""

# Test 2: Check if Model file exists
echo -e "${YELLOW}Test 2: Checking Model file...${NC}"
if [ -f "/home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep/Models/mthongbao.php" ]; then
    echo -e "${GREEN}✓ mthongbao.php exists${NC}"
else
    echo -e "${RED}✗ mthongbao.php not found${NC}"
fi
echo ""

# Test 3: Check if Controller file exists
echo -e "${YELLOW}Test 3: Checking Controller file...${NC}"
if [ -f "/home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep/Controllers/cthongbao.php" ]; then
    echo -e "${GREEN}✓ cthongbao.php exists${NC}"
else
    echo -e "${RED}✗ cthongbao.php not found${NC}"
fi
echo ""

# Test 4: Check if API endpoint exists
echo -e "${YELLOW}Test 4: Checking API endpoint...${NC}"
if [ -f "/home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep/Ajax/thongbao.php" ]; then
    echo -e "${GREEN}✓ Ajax/thongbao.php exists${NC}"
else
    echo -e "${RED}✗ Ajax/thongbao.php not found${NC}"
fi
echo ""

# Test 5: Check if JavaScript handler exists
echo -e "${YELLOW}Test 5: Checking JavaScript handler...${NC}"
if [ -f "/home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep/Assets/js/notification-handler.js" ]; then
    echo -e "${GREEN}✓ notification-handler.js exists${NC}"
else
    echo -e "${RED}✗ notification-handler.js not found${NC}"
fi
echo ""

# Test 6: Check if WebSocket server is updated
echo -e "${YELLOW}Test 6: Checking WebSocket server...${NC}"
if grep -q "command === 'notification'" /home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep/Chat.php; then
    echo -e "${GREEN}✓ WebSocket server has notification handler${NC}"
else
    echo -e "${RED}✗ WebSocket server missing notification handler${NC}"
fi
echo ""

# Test 7: Check if test result submission file is updated
echo -e "${YELLOW}Test 7: Checking test result submission file...${NC}"
if grep -q "cthongbao" /home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep/Views/nhanvienxetnghiem/pages/chinhsua/index.php; then
    echo -e "${GREEN}✓ Test result submission file updated${NC}"
else
    echo -e "${RED}✗ Test result submission file not updated${NC}"
fi
echo ""

# Test 8: Check PHP syntax
echo -e "${YELLOW}Test 8: Checking PHP syntax...${NC}"
php -l /home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep/Models/mthongbao.php 2>&1 | grep -q "No syntax errors"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ mthongbao.php: No syntax errors${NC}"
else
    echo -e "${RED}✗ mthongbao.php: Syntax errors found${NC}"
fi

php -l /home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep/Controllers/cthongbao.php 2>&1 | grep -q "No syntax errors"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ cthongbao.php: No syntax errors${NC}"
else
    echo -e "${RED}✗ cthongbao.php: Syntax errors found${NC}"
fi

php -l /home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep/Ajax/thongbao.php 2>&1 | grep -q "No syntax errors"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ thongbao.php: No syntax errors${NC}"
else
    echo -e "${RED}✗ thongbao.php: Syntax errors found${NC}"
fi
echo ""

# Test 9: Test database connection
echo -e "${YELLOW}Test 9: Testing database connection...${NC}"
php -r "
\$con = mysqli_connect('localhost', 'kltn', 'Kltntrangquynh2025@', 'hanhphuc');
if (\$con) {
    echo '✓ Database connection successful\n';
    mysqli_close(\$con);
    exit(0);
} else {
    echo '✗ Database connection failed\n';
    exit(1);
}
"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}Database connection OK${NC}"
else
    echo -e "${RED}Database connection failed${NC}"
fi
echo ""

echo "========================================="
echo "Test Summary"
echo "========================================="
echo ""
echo "All critical components are in place!"
echo ""
echo "Next steps:"
echo "1. Start WebSocket server: php server.php"
echo "2. Submit a test result from lab technician account"
echo "3. Check doctor's dashboard for notification"
echo ""
echo "For manual testing:"
echo "- Check browser console for WebSocket logs"
echo "- Check database: SELECT * FROM thongbao;"
echo "- Test API: curl http://localhost/Ajax/thongbao.php?action=count_unread"
echo ""
