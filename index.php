<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DPD Exercise</title>
  <style>
    h1 {
      text-align: center;
      font-size: 30px;
      font-family: Helvetica;
    }

    body {
      margin: 0 auto;
      width: 40%;
    }

    .error {
      color: red;
    }
  </style>
</head>

<body>
  <h1>Telek kerítés kalkulátor</h1>


  <p class="error"><?= htmlspecialchars($_GET['calculation']) ?> </p>

  <form action="include/calculate.inc.php" method="POST">
    <p>Point A</p>
    <label for="latitudeA">Latitude: </label>
    <input type="text" name="latitudeA" id="latitudeA" value="-32.8830055">
    <label for="longitudeA">Longitude: </label>
    <input type="text" name="longitudeA" id="longitudeA" value="151.686214"><br>

    <p>Point B</p>
    <label for="latitudeB">Latitude: </label>
    <input type="text" name="latitudeB" id="latitudeB" value="-32.9757551">
    <label for="longitudeB">Longitude: </label>
    <input type="text" name="longitudeB" id="longitudeB" value="151.827158"><br><br>

    <button type="submit" name="calculate-submit">Calculate</button>

  </form>

  <p>PointC: <?= $_GET['pointC'] ?></p>
  <p>PointD: <?= $_GET['pointD'] ?></p>
  <p>Perimeter: <?= $_GET['perimeter'] ?> meter</p>
  <p>Area: <?= $_GET['area'] ?> squaremeter</p>
  <p>Price: <?= $_GET['price'] ?> EUR</p>

</body>

</html>