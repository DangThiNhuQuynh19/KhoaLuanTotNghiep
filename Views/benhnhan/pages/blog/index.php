<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tin Tức - LifeCare Hospital</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Hiệu ứng card xuất hiện */
    @keyframes fadeUp {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    .news-card {
      animation: fadeUp 0.6s ease forwards;
    }
    .news-card:hover img {
      transform: scale(1.05);
    }
    .search-bar {
      background: white;
      padding: 15px;
      margin: 20px auto;
      max-width: 900px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      border-radius: 12px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.15);
      justify-content: center;
    }
    .search-bar input[type="text"], 
    .search-bar input[type="date"] {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      width: 250px;
      font-size: 16px;
    }
    .search-bar button {
      background: #5e4b93;
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: 0.3s;
    }
    .search-bar button:hover {
      background: #4c3b7d;
      transform: scale(1.05);
    }
  </style>
</head>
<body class="bg-gray-50 font-sans">
  <!-- Banner -->
  <section class="relative">
    <img src="Assets/img/banner-blog.png" class="w-full object-cover h-64 md:h-80 rounded-b-2xl shadow-lg" style ="margin-top: 100px;">
    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
      <h2 class="text-white text-3xl md:text-4xl font-bold">Tin Tức & Sự Kiện</h2>
    </div>
  </section>
  <div class="search-bar">
    <input type="text" id="keyword" placeholder="Tìm theo tiêu đề, nội dung...">
    <input type="date" id="date">
    <button onclick="filterNews()">🔍 Tìm kiếm</button>
  </div>
  <!-- Danh sách tin -->
  <main class="max-w-6xl mx-auto px-4 py-10 grid gap-8 md:grid-cols-3">
    <!-- Card tin -->
    
    <article class="news-card bg-white rounded-2xl shadow hover:shadow-xl transition duration-300 overflow-hidden">
      <img src="Assets/img/hoithaoquocte.jpg" class="w-full h-48 object-cover transition duration-300">
      <div class="p-4">
        <h3 class="text-xl font-semibold text-[#5e4b93]">Bác sĩ LifeCare tham dự hội thảo quốc tế</h3>
        <p class="text-gray-600 mt-2 text-sm">Hội thảo về chăm sóc sức khỏe hiện đại với sự góp mặt của nhiều chuyên gia y tế hàng đầu...</p>
        <a href="#" class="inline-block mt-3 text-[#5e4b93] font-semibold hover:underline">Đọc thêm →</a>
      </div>
    </article>

    <article class="news-card bg-white rounded-2xl shadow hover:shadow-xl transition duration-300 overflow-hidden">
      <img src="Assets/img/khammienphi.jpg" class="w-full h-48 object-cover transition duration-300">
      <div class="p-4">
        <h3 class="text-xl font-semibold text-[#5e4b93]">Chương trình khám sức khỏe miễn phí</h3>
        <p class="text-gray-600 mt-2 text-sm">LifeCare tổ chức khám tổng quát miễn phí cho người dân địa phương vào cuối tuần này...</p>
        <a href="#" class="inline-block mt-3 text-[#5e4b93] font-semibold hover:underline">Đọc thêm →</a>
      </div>
    </article>

    <article class="news-card bg-white rounded-2xl shadow hover:shadow-xl transition duration-300 overflow-hidden">
      <img src="Assets/img/updatekythuat.jpg" class="w-full h-48 object-cover transition duration-300">
      <div class="p-4">
        <h3 class="text-xl font-semibold text-[#5e4b93]">Cập nhật kỹ thuật điều trị mới</h3>
        <p class="text-gray-600 mt-2 text-sm">Bệnh viện vừa triển khai phương pháp điều trị tiên tiến giúp rút ngắn thời gian hồi phục...</p>
        <a href="#" class="inline-block mt-3 text-[#5e4b93] font-semibold hover:underline">Đọc thêm →</a>
      </div>
    </article>
  </main>
