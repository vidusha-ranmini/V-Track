<!DOCTYPE html>
<html lang="en">
<head>

    
    <!-- <link rel="stylesheet" href="<?= base_url('styles.css') ?>"> -->

    <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <!-- site metas -->
      <title>V-Track</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- bootstrap css -->
      <link rel="stylesheet" type="text/css" href="<?= base_url('css/bootstrap.min.css') ?>">
      <!-- style css -->
      <link rel="stylesheet" type="text/css" href="<?= base_url('css/style.css') ?>">
      <!-- Responsive-->
      <link rel="stylesheet" href="<?= base_url('css/responsive.css') ?>">
      <!-- fevicon -->
      <link rel="icon" href="<?= base_url('images/fevicon.png') ?>" type="image/gif" />
      <!-- fonts -->
      <link href="https://fonts.googleapis.com/css?family=Poppins:400,700|Sen:400,700,800&display=swap" rel="stylesheet">
      <!-- Scrollbar Custom CSS -->
      <link rel="stylesheet" href="<?= base_url('css/jquery.mCustomScrollbar.min.css') ?>">
      <!-- Tweaks for older IEs-->
      <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        body {
            background: url('<?= base_url('images/banner-bg.png') ?>') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .landing-flex {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100vw;
            height: 100vh;
        }
        .landing-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(255,255,255,0.85);
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(25, 118, 210, 0.10);
            padding: 48px 56px 40px 56px;
            min-width: 340px;
            max-width: 480px;
        }
        .vtrack-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1976d2;
            margin-bottom: 10px;
            text-align: center;
            width: 100%;
        }
        .vtrack-subtitle {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 24px;
            text-align: center;
            width: 100%;
        }
        .login-btn {
            margin-top: 0;
            align-self: center;
        }
        .landing-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            height: 100%;
            margin-left: 40px;
        }
        .banner_img img {
            max-width: 400px;
            width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(25, 118, 210, 0.10);
        }
        @media (max-width: 900px) {
            .landing-flex {
                flex-direction: column;
                height: auto;
            }
            .landing-right {
                margin-left: 0;
                margin-top: 32px;
            }
        }
    </style>
    </head>

</head>
<body>
    <div class="landing-flex">
        <div class="landing-left">
            <div class="vtrack-title">Welcome to <br>V-Track</div>
            <div class="vtrack-subtitle">Efficiently manage and access village family records with ease.</div>
            <div class="read_bt"><a href="<?= base_url('login') ?>">Login</a></div>
        </div>
        <div class="landing-right">
            <div class="banner_img"><img src="<?= base_url('images/banner-img.png') ?>" alt="V-Track Banner"></div>
        </div>
    </div>
</body>
</html>
