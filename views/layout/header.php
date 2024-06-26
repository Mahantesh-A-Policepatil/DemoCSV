<!DOCTYPE html>
<html lang="en">

<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
</head>

<body>
  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand active" href="#">Product Demo</a>
      </div>
      <ul class="nav navbar-nav float-right" style="margin-left:1140px;">
        <!-- <li class="active"><a href="#">Home</a></li> -->
        <?php
        if (isset($_SESSION['valid'])) {
        ?>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo ucfirst($_SESSION['name']) ?> <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="../product/view.php">Products</a></li>
              <li><a href="../product/importData.php">Import CSV</a></li>
              <li><a href="../user/update.php">Update profile</a></li>
              <li><a href="../user/logout.php">Logout</a></li>
            </ul>
          </li>
        <?php
        }
        ?>
      </ul>
    </div>
  </nav>
</body>

</html>