# System Architecture Diagram - Push Notification System

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      PUSH NOTIFICATION SYSTEM FLOW                          │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────────────┐         ┌──────────────────┐         ┌──────────────────┐
│                  │         │                  │         │                  │
│  Lab Technician  │         │   Doctor's       │         │   Database       │
│   (Browser)      │         │   Browser        │         │   (MySQL)        │
│                  │         │                  │         │                  │
└────────┬─────────┘         └────────┬─────────┘         └────────┬─────────┘
         │                            │                            │
         │ 1. Submit Test Results     │                            │
         ├──────────────────────────> │                            │
         │    (POST chinhsua/index.php)                            │
         │                            │                            │
         │                   ┌────────▼───────────┐                │
         │                   │                    │                │
         │                   │  PHP Backend       │                │
         │                   │  (cthongbao.php)   │ 2. Save        │
         │                   │                    │ Notification   │
         │                   └────────┬───────────┘ ──────────────>│
         │                            │                            │
         │                            │ 3. Get Doctor Info         │
         │                            │ <──────────────────────────┤
         │                            │                            │
         │                   ┌────────▼───────────┐                │
         │                   │                    │                │
         │                   │  WebSocket Server  │                │
         │                   │  (Chat.php:8080)   │                │
         │                   │                    │                │
         │                   └────────┬───────────┘                │
         │                            │                            │
         │                            │ 4. Send Notification       │
         │                            │ (command: 'notification')  │
         │                            ├──────────────────────────> │
         │                            │                            │
         │              ┌─────────────▼──────────────┐             │
         │              │                            │             │
         │              │  JavaScript Handler        │             │
         │              │  (notification-handler.js) │             │
         │              │                            │             │
         │              └─────────────┬──────────────┘             │
         │                            │                            │
         │                   ┌────────▼───────────┐                │
         │                   │  5a. Browser       │                │
         │                   │  Notification      │                │
         │                   └────────────────────┘                │
         │                            │                            │
         │                   ┌────────▼───────────┐                │
         │                   │  5b. Toast         │                │
         │                   │  Notification      │                │
         │                   └────────────────────┘                │
         │                            │                            │
         │                   ┌────────▼───────────┐                │
         │                   │  5c. Update        │                │
         │                   │  Badge Counter     │                │
         │                   └────────────────────┘                │
         │                            │                            │
         │                            │ 6. Click "View Result"     │
         │                            ├──────────────────────────> │
         │                            │ Mark as Read (API call)    │
         │                            │                            │
         │                            │ 7. Navigate to Results     │
         │                            │ Page                       │
         │                            │                            │
         └────────────────────────────┴────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────────┐
│                          COMPONENT DETAILS                                  │
└─────────────────────────────────────────────────────────────────────────────┘

DATABASE LAYER (MySQL)
├── thongbao table
│   ├── mathongbao (PK)
│   ├── manguoidung (FK -> nguoidung)
│   ├── tieude
│   ├── noidung
│   ├── loaithongbao
│   ├── malichxetnghiem (FK -> lichxetnghiem)
│   ├── daxem (boolean)
│   └── ngaytao (timestamp)

PHP BACKEND
├── Models/mthongbao.php
│   ├── insert_thongbao()
│   ├── select_thongbao_by_manguoidung()
│   ├── count_thongbao_chuaxem()
│   ├── update_thongbao_daxem()
│   ├── get_bacsi_from_lichxetnghiem()
│   └── delete_thongbao()
│
├── Controllers/cthongbao.php
│   ├── create_thongbao()
│   ├── get_thongbao_by_manguoidung()
│   ├── count_thongbao_chuaxem()
│   ├── mark_thongbao_as_read()
│   ├── send_test_result_notification()
│   └── send_websocket_notification()
│
└── Ajax/thongbao.php (REST API)
    ├── GET ?action=get_all
    ├── GET ?action=count_unread
    ├── GET ?action=mark_read
    ├── GET ?action=mark_all_read
    └── GET ?action=delete

WEBSOCKET SERVER (Port 8080)
├── Chat.php
│   ├── Command: 'register'
│   │   └── Register user connection
│   ├── Command: 'send'
│   │   └── Send chat message
│   └── Command: 'notification'
│       └── Broadcast push notification

JAVASCRIPT CLIENT
├── Assets/js/notification-handler.js
│   ├── NotificationHandler class
│   │   ├── connect() - WebSocket connection
│   │   ├── attemptReconnect() - Auto reconnect
│   │   ├── handleNotification() - Process notification
│   │   ├── showBrowserNotification() - Native notification
│   │   ├── showPageNotification() - Toast display
│   │   ├── updateNotificationBadge() - Update counter
│   │   ├── markAsRead() - Mark notification read
│   │   └── playNotificationSound() - Audio alert
│   └── Auto-initialization on page load

UI COMPONENTS
├── Views/bacsi/layout/header.php
│   ├── Notification bell icon
│   ├── Badge counter (red circle)
│   └── Script inclusion
│
└── In-page Toast Notification
    ├── Animated slide-in from right
    ├── Success icon
    ├── Title and message
    ├── "View Result" button
    ├── "Close" button
    └── Auto-dismiss after 10s


┌─────────────────────────────────────────────────────────────────────────────┐
│                        MESSAGE FLOW DIAGRAM                                 │
└─────────────────────────────────────────────────────────────────────────────┘

WEBSOCKET MESSAGE STRUCTURE:

1. REGISTER (Client → Server)
   {
     "command": "register",
     "username": "BS001"
   }

2. NOTIFICATION (Server → Client)
   {
     "command": "notification",
     "type": "ketquaxetnghiem",
     "receiver": "BS001",
     "title": "Kết quả xét nghiệm đã có",
     "content": "Kết quả xét nghiệm cho lịch #123 đã được cập nhật",
     "malichxetnghiem": 123,
     "mathongbao": 456,
     "timestamp": "2025-12-08 17:30:00"
   }

3. API RESPONSE (Server → Client via HTTP)
   {
     "success": true,
     "data": [...],
     "count": 5
   }


┌─────────────────────────────────────────────────────────────────────────────┐
│                         DEPLOYMENT CHECKLIST                                │
└─────────────────────────────────────────────────────────────────────────────┘

□ 1. Create database table
     $ mysql -u kltn -p hanhphuc < database/migrations/create_thongbao_table.sql

□ 2. Start WebSocket server
     $ cd /path/to/project
     $ php server.php
     (Keep running in background or use screen/tmux)

□ 3. Update server.php if needed
     - Set correct host/port
     - Configure SSL if using HTTPS

□ 4. Test WebSocket connection
     - Open browser console
     - Check for WebSocket connection logs

□ 5. Test notification flow
     - Login as lab technician
     - Submit test results
     - Login as doctor
     - Verify notification appears

□ 6. Grant browser notification permission
     - Click "Allow" when prompted
     - Test browser notifications

□ 7. Monitor logs
     - Check WebSocket server logs
     - Check PHP error logs
     - Check browser console

□ 8. Performance monitoring
     - Monitor WebSocket connections
     - Check database query performance
     - Monitor notification delivery time
```
