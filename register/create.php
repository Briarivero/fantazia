<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>All love lain.</title>

  <link rel="icon" type="image/png" href="favicon_64px.png" sizes="64x64" />
  <link rel="icon" type="image/png" href="favicon_32px.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="favicon_16px.png" sizes="16x16" />

  <link rel="stylesheet" type="text/css" href="/styles/stylescreate.css" />
</head>

<body>
  <div class="wiredGang">
    <div class="opacity">
      <div class="opacityContent2">
        <form name="createForm" method="post" action="register.php">
          <p>User ID:</p>
          <input
            class="text"
            type="text"
            name="username"
            size="25"
            placeholder="michiko" />
          <br />
          <p>Email:</p>
          <input
            class="text"
            type="email"
            name="email"
            size="25"
            placeholder="michiko@gmail.com"
            required />
          <br />
          <p>PassWord:</p>
          <input
            class="text"
            type="password"
            size="25"
            name="password"
            placeholder="••••••••••••"
            required />
          <br />
          <p>Confirm Pass:</p>
          <input
            class="text"
            type="password"
            size="25"
            name="confirm_password"
            placeholder="••••••••••••"
            required />
          <br />
          <button class="button" type="submit">Create
          </button>
        </form>
        <?php
        session_start();
        if (isset($_SESSION['error'])) {
          echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
          unset($_SESSION['error']);
        }
        ?>
        <p class="descp">
          <b>NOTE:</b> All love lain.All love lain.All love lain.All love lain. All love lain.All love lain.All love lain.All love lain. All love lain.All love lain.All love lain.All love lain. All love lain.
        </p>
        <br />
        <br />
        <a href="../lainalone.html">Help & Guidelines</a>
        <br />
        <img src="/img/gif/LainLaugh.gif" alt="wired" />
      </div>

    </div>
  </div>
  </div>
  </div>
</body>

</html>