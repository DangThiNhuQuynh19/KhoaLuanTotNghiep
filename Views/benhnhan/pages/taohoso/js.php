<script>
    // Dữ liệu xã phường
    const xaphuongs = <?php echo json_encode($xaphuong_list); ?>;

    function toggleGuardian() {
        const dobInput = document.getElementById("dob");
        if (!dobInput.value) return;

        const birth = new Date(dobInput.value);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }

        // Hiển thị thông tin tuổi
        const ageDisplay = document.getElementById("age-display");
        ageDisplay.innerHTML = `<i class="fas fa-birthday-cake"></i> Tuổi: ${age}`;
        ageDisplay.classList.add("show");

        // Các elements cần điều khiển
        const contactSection = document.getElementById("contact-section");
        const jobSection = document.getElementById("job-section");
        const cccdSection = document.getElementById("cccd-section");
        const birthCertSection = document.getElementById("birth-cert-section");
        const guardianInfo = document.getElementById("guardian-info");
        const emailInput = document.getElementById("email");
        const sdtInput = document.getElementById("sdt");
        const cccdInput = document.getElementById("cccd");
        const jobSelect = document.getElementById("job");
        if (age < 16) {
            // Trẻ em dưới 16 tuổi
            contactSection.style.display = "none";
            jobSection.style.display = "none";
            cccdSection.classList.add("hidden");
            jobSection.classList.add("hidden");
            birthCertSection.classList.remove("hidden");


            
            // Bỏ required cho email, sdt, cccd
            emailInput.removeAttribute("required");
            sdtInput.removeAttribute("required");
            cccdInput.removeAttribute("required");
            jobSelect.removeAttribute("required");
        } else {
            // Từ 16 tuổi trở lên
            contactSection.style.display = "block";
            cccdSection.classList.remove("hidden");
            birthCertSection.classList.add("hidden");
            
            // Thêm required cho email, sdt, cccd
            emailInput.setAttribute("required", "required");
            sdtInput.setAttribute("required", "required");
            cccdInput.setAttribute("required", "required");
        }

        // Hiển thị thông tin giám hộ
        if (age < 18 || age > 60) {
            guardianInfo.style.display = "block";
            setGuardianRequired(true);
        } else {
            guardianInfo.style.display = "none";
            setGuardianRequired(false);
        }
    }


    function previewImage(input, previewId) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);
        
        if (!file) {
            preview.style.display = "none";
            return;
        }

        if (!file.type.startsWith("image/")) {
            Swal.fire({
                title: 'Lỗi!',
                text: 'Vui lòng chọn đúng định dạng ảnh (jpg, png, jpeg)',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            input.value = "";
            preview.style.display = "none";
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(file);
    }

    function toggleOtherJob() {
        const jobSelect = document.getElementById("job");
        const jobOtherGroup = document.getElementById("job-other-group");
        const jobOtherInput = document.getElementById("job-other");
        
        if (jobSelect.value === "Khác") {
            jobOtherGroup.classList.remove("hidden");
            jobOtherInput.setAttribute("required", "required");
        } else {
            jobOtherGroup.classList.add("hidden");
            jobOtherInput.removeAttribute("required");
            jobOtherInput.value = "";
        }
    }

    function loadXaPhuong() {
        const tinhSelect = document.getElementById("tinh");
        const xaSelect = document.getElementById("xa");
        const mathanhpho = tinhSelect.value;

        xaSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
        
        if (!mathanhpho) return;

        const xaphuongs_matinh = xaphuongs.filter(p => p.matinhthanhpho === mathanhpho);
        xaphuongs_matinh.forEach(h => {
            const option = document.createElement('option');
            option.value = h.maxaphuong;
            option.textContent = h.tenxaphuong;
            xaSelect.appendChild(option);
        });
    }

    function gh_loadXaPhuong() {
        const tinhSelect = document.getElementById("gh_tinh");
        const xaSelect = document.getElementById("gh_xa");
        const mathanhpho = tinhSelect.value;

        xaSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
        
        if (!mathanhpho) return;

        const xaphuongs_matinh = xaphuongs.filter(p => p.matinhthanhpho === mathanhpho);
        xaphuongs_matinh.forEach(h => {
            const option = document.createElement('option');
            option.value = h.maxaphuong;
            option.textContent = h.tenxaphuong;
            xaSelect.appendChild(option);
        });
    }

    // Khởi tạo khi trang load
    document.addEventListener('DOMContentLoaded', function() {
        // Nếu có giá trị ngày sinh từ POST, trigger toggleGuardian
        const dobInput = document.getElementById("dob");
        if (dobInput.value) {
            toggleGuardian();
        }
        
        // Nếu có giá trị job từ POST, trigger toggleOtherJob
        const jobSelect = document.getElementById("job");
        if (jobSelect.value === "Khác") {
            toggleOtherJob();
        }
    });
</script>