<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quarter Categories | CFTRI Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #e8eff7, #f5f7fa);
    font-family: 'Segoe UI', sans-serif;
    min-height: 100vh;
    padding: 40px;
    animation: fadeInBody 0.7s ease-in-out;
}
@keyframes fadeInBody {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
h4 {
    color: #0b2341;
    font-weight: 700;
    margin-bottom: 30px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.category-container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 25px;
}
.card-type {
    width: 160px;
    height: 150px;
    background: rgba(255, 255, 255, 0.85);
    border-radius: 16px;
    backdrop-filter: blur(6px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    text-align: center;
    text-decoration: none;
    color: #0b2341;
    transition: all 0.35s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}
.card-type::before {
    content: "";
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at 0 0, rgba(255,255,255,0.3), transparent 60%);
    transform: rotate(45deg);
    transition: all 0.6s ease;
}
.card-type:hover::before {
    top: -30%;
    left: -30%;
}
.card-type i {
    font-size: 36px;
    margin-bottom: 10px;
    transition: transform 0.3s ease, color 0.3s ease;
}
.card-type span {
    font-weight: 600;
    font-size: 16px;
}
.card-type:hover {
    background: linear-gradient(135deg, #16375f, #3279d8);
    color: #fff;
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
.card-type:hover i {
    transform: scale(1.2) rotate(5deg);
    color: #ffeb3b;
}
.footer-note {
    margin-top: 40px;
    text-align: center;
    color: #4b4b4b;
    font-size: 14px;
}
@keyframes floatIcon {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-5px); }
  100% { transform: translateY(0px); }
}
.card-type i {
  animation: floatIcon 2s ease-in-out infinite;
}
</style>
</head>

<body>

<h4><i class="fa-solid fa-city me-2"></i> Quarter Categories</h4>

<div class="category-container">
  <a href="quarters_A.php" class="card-type">
    <i class="fa-solid fa-house"></i>
    <span>Type A</span>
  </a>
  <a href="quarters_B.php" class="card-type">
    <i class="fa-solid fa-building"></i>
    <span>Type B</span>
  </a>
  <a href="quarters_C.php" class="card-type">
    <i class="fa-solid fa-house-chimney"></i>
    <span>Type C</span>
  </a>
  <a href="quarters_D.php" class="card-type">
    <i class="fa-solid fa-hotel"></i>
    <span>Type D</span>
  </a>
  <a href="quarters_E.php" class="card-type">
    <i class="fa-solid fa-landmark"></i>
    <span>Type E</span>
  </a>
  <a href="quarters_F.php" class="card-type">
    <i class="fa-solid fa-city"></i>
    <span>Type F</span>
  </a>
</div>

<p class="footer-note">© CFTRI Quarter Management System | Designed for Administrative Use</p>

</body>
</html>
