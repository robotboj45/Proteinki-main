<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Split Diagonal Transitions</title>
    <link rel="stylesheet" href="start/start-style.css">
</head>
<body>
  <?php
  // Ścieżki do odpowiednich lokalizacji
  $loginLocalPath = 'account/login.html';
  $homeUrl = 'index.php';

  $loginTitle = 'Logowanie';
  $homeTitle = 'Strona Główna';
  ?>

  <div class="oblique">
    <div class="main-block-oblique skew-block">

      <div class="skew-block-repeat">
        <a href="#" id="loginLink">
          <div class="oblique-inner">
            <div class="image-wrapper">
              <div class="main-image">
                <img src="start/crossfit.jpg" class="image-img" alt="">
              </div>
            </div>
          </div>
          <div class="oblique-caption caption-top">
            <h2><?php echo $loginTitle; ?></h2>
            <button onclick="window.location.href='<?php echo $loginLocalPath; ?>'">Otwórz</button>
          </div>
        </a>
      </div>

      <div class="skew-block-repeat">
        <a href="#" id="homeLink">
          <div class="oblique-inner">
            <div class="image-wrapper">
              <div class="main-image">
                <img src="start/prot.jpg" class="image-img" alt="">
              </div>
            </div>
          </div>
          <div class="oblique-caption caption-top">
            <h2><?php echo $homeTitle; ?></h2>
            <button onclick="window.location.href='<?php echo $homeUrl; ?>'">Otwórz</button>
          </div>
        </a>
      </div>

    </div>
  </div>
</body>
</html>
