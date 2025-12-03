<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Hồ Sơ Bệnh Án</title>
    <link rel="stylesheet" href="Views/bacsi/assets/css/csschitiethoso.css">
    <!-- Add inline CSS for modals to ensure they display correctly -->
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease-out;
            max-height: 80vh;
            overflow-y: auto;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-content h2 {
            color: #343177;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 24px;
            border-bottom: 2px solid #343177;
            padding-bottom: 10px;
        }

        .close {
            color: #999;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #000;
        }

        .info-row {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            color: #333;
            min-width: 150px;
        }

        .info-value {
            color: #666;
            word-break: break-word;
            flex: 1;
            text-align: right;
        }

        /* Button Styles */
        .btn-primary {
            background-color: #343177;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
        }

        .btn-primary:hover {
            background-color: #552d7d;
        }

        .btn-outline {
            background-color: transparent;
            color: #343177;
            border: 1px solid #343177;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .btn-outline:hover {
            background-color: #343177;
            color: white;
        }

        /* Form Styles in Modal */
        .update-tabs {
            margin-top: 20px;
        }

        .tab-header {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #ddd;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .update-tab-link {
            padding: 12px 20px;
            cursor: pointer;
            background-color: #f5f5f5;
            border: none;
            border-bottom: 3px solid transparent;
            color: #333;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .update-tab-link:hover {
            background-color: #efefef;
        }

        .update-tab-link.active {
            background-color: white;
            border-bottom-color: #343177;
            color: #343177;
            font-weight: bold;
        }

        .update-tab-content {
            display: none;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .update-tab-content.active {
            display: block;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #343177;
            box-shadow: 0 0 5px rgba(52, 49, 119, 0.3);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-col {
            min-width: 0;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 95%;
                margin: 20% auto;
                padding: 20px;
            }

            .tab-header {
                flex-direction: column;
            }

            .update-tab-link {
                width: 100%;
                border-bottom: none;
                border-left: 3px solid transparent;
            }

            .update-tab-link.active {
                border-left-color: #343177;
                border-bottom-color: transparent;
            }

            .info-row {
                flex-direction: column;
            }

            .info-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Modal Cập nhật hồ sơ -->
    <div id="modalcapnhathoso" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateRecordModal()">&times;</span>
            <h2>Cập nhật hồ sơ bệnh án</h2>
            <form action="" method="post" id="prescriptionForm">
                <!-- Add onclick events to tab links and proper active state management -->
                <div class="update-tabs">
                    <div class="tab-header">
                        <button type="button" class="update-tab-link active" onclick="openUpdateTab(event, 'update-prescription')">Thêm đơn thuốc</button>
                        <button type="button" class="update-tab-link" onclick="openUpdateTab(event, 'update-test')">Thêm lịch xét nghiệm</button>
                        <button type="button" class="update-tab-link" onclick="openUpdateTab(event, 'update-diagnosis')">Thêm chẩn đoán</button>
                    </div>
                    <div id="update-prescription" class="update-tab-content active">
                        <!-- Prescription form content here -->
                    </div>
                    <div id="update-test" class="update-tab-content">
                        <!-- Test form content here -->
                    </div>
                    <div id="update-diagnosis" class="update-tab-content">
                        <!-- Diagnosis form content here -->
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Chẩn đoán -->
    <div id="modalchuandoan" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeChuanDoanPopup()">&times;</span>
            <h2>Chi tiết chẩn đoán</h2>
            <div class="info-row">
                <div class="info-label">Ngày khám:</div>
                <div class="info-value" id="cd-ngaykham">N/A</div>
            </div>
            <div class="info-row">
                <div class="info-label">Bác sĩ:</div>
                <div class="info-value" id="cd-bacsi">N/A</div>
            </div>
            <div class="info-row">
                <div class="info-label">Triệu chứng ban đầu:</div>
                <div class="info-value" id="cd-trieuchung">N/A</div>
            </div>
            <div class="info-row">
                <div class="info-label">Chẩn đoán:</div>
                <div class="info-value" id="cd-chandoan">N/A</div>
            </div>
            <div class="info-row">
                <div class="info-label">Hướng điều trị:</div>
                <div class="info-value" id="cd-kehoachdieutri">N/A</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kết luận:</div>
                <div class="info-value" id="cd-ketluan">N/A</div>
            </div>
            <button class="btn-primary" onclick="printChuanDoan()">In Chẩn Đoán</button>
        </div>
    </div>

    <script>
        function openUpdateTab(event, tabName) {
            event.preventDefault();
            
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.update-tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tab links
            const tabLinks = document.querySelectorAll('.update-tab-link');
            tabLinks.forEach(link => {
                link.classList.remove('active');
            });
            
            // Show selected tab and mark it as active
            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.classList.add('active');
            }
            
            // Mark clicked tab link as active
            event.target.classList.add('active');
        }

        function openUpdateRecordModal() {
            const modal = document.getElementById("modalcapnhathoso");
            if (modal) {
                modal.style.display = "block";
                // Reset to first tab when opening
                const firstTab = document.querySelector('.update-tab-content');
                if (firstTab) {
                    firstTab.classList.add('active');
                }
            } else {
                console.error("[v0] Modal element not found: modalcapnhathoso");
            }
        }

        function closeUpdateRecordModal() {
            const modal = document.getElementById("modalcapnhathoso");
            if (modal) {
                modal.style.display = "none";
            }
        }

        function openChuanDoanPopup(chitiet) {
            console.log("[v0] Opening diagnosis popup with data:", chitiet);
            
            const modal = document.getElementById("modalchuandoan");
            if (!modal) {
                console.error("[v0] Modal element not found: modalchuandoan");
                alert("Lỗi: Không thể mở popup chi tiết!");
                return;
            }
            
            // Display modal
            modal.style.display = "block";
            
            // Update information with error handling
            try {
                document.getElementById("cd-ngaykham").textContent = chitiet[0]?.ngaykham || "N/A";
                document.getElementById("cd-bacsi").textContent = chitiet[0]?.hoten || "N/A";
                document.getElementById("cd-trieuchung").textContent = chitiet[0]?.trieuchungbandau || "N/A";
                document.getElementById("cd-chandoan").textContent = chitiet[0]?.chandoan || "N/A";
                document.getElementById("cd-kehoachdieutri").textContent = chitiet[0]?.huongdieutri || "N/A";
                document.getElementById("cd-ketluan").textContent = chitiet[0]?.ketluan || "N/A";
            } catch (error) {
                console.error("[v0] Error populating modal data:", error);
                alert("Lỗi khi hiển thị dữ liệu!");
            }
        }

        function closeChuanDoanPopup() {
            const modal = document.getElementById("modalchuandoan");
            if (modal) {
                modal.style.display = "none";
            }
        }

        function printChuanDoan() {
            const ngaykham = document.getElementById("cd-ngaykham").textContent;
            const bacsi = document.getElementById("cd-bacsi").textContent;
            const trieuchung = document.getElementById("cd-trieuchung").textContent;
            const chandoan = document.getElementById("cd-chandoan").textContent;
            const kehoachdieutri = document.getElementById("cd-kehoachdieutri").textContent;
            const ketluan = document.getElementById("cd-ketluan").textContent;
            
            const originalContents = document.body.innerHTML;
            
            document.body.innerHTML = `
                <div style="padding: 20px; font-family: Arial, sans-serif;">
                    <h1 style="text-align: center; margin-bottom: 30px;">Chi Tiết Chẩn Đoán và Hướng Điều Trị</h1>
                    
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; width: 30%;">Ngày khám:</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">${ngaykham}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Bác sĩ:</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">${bacsi}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Triệu chứng ban đầu:</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">${trieuchung}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Chẩn đoán:</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">${chandoan}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Hướng điều trị:</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">${kehoachdieutri}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Kết luận:</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">${ketluan}</td>
                        </tr>
                    </table>
                </div>
            `;
            
            window.print();
            document.body.innerHTML = originalContents;
        }

        window.onclick = function(event) {
            const modals = [
                "modalchuandoan",
                "modalcapnhathoso",
                "modalxetnghiem",
                "modalchitietdonthuoc",
                "modaltaodonthuoc"
            ];
            
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && event.target === modal) {
                    modal.style.display = "none";
                }
            });
        };
    </script>
</body>
</html>
