<?php
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    if (isset($_POST['calculate-submit'])) {

        if (empty($_POST['latitudeA']) || empty($_POST['longitudeA']) || empty($_POST['latitudeB']) || empty($_POST['longitudeB'])) {
            header("Location: ../index.php?calculation=You did not fill in all fields!");
            exit();
        } else {

            if (!isGeoValid($_POST['latitudeA'] . ',' . $_POST['longitudeB'])) {

                header("Location: ../index.php?calculation=Invalid A Point!");
                exit();
            } elseif (!isGeoValid($_POST['latitudeB'] . ',' . $_POST['longitudeB'])) {

                header("Location: ../index.php?calculation=Invalid B Point!");
                exit();
            } else {

                $pointA = [$_POST['latitudeA'], $_POST['longitudeA']];
                $pointC = [$_POST['latitudeB'], $_POST['longitudeB']];

                $rectangle = new RectangleArea($pointA, $pointC);

                $pointC = $rectangle->getPointC();
                $pointD = $rectangle->getPointD();

                $perimeter = $rectangle->getPerimeter();
                $area = $rectangle->getArea();

                $settings = parse_ini_file("settings.ini", true);

                $doorLength = $settings['door']['length'];
                $cornerLength = $settings['corner']['length'];
                $pillarLength = $settings['pillar']['length'];
                $wireLength = $settings['wire']['length'];

                $minimumLength = 2 * $pillarLength + 2 * $cornerLength + $doorLength;
                $wirePillarLength = $wireLength + $pillarLength;

                $initialElementSet = [
                    'door' => 4,
                    'corner' => 4,
                    'wire' => 0,
                    'pillar' => 8
                ];

                $width = $rectangle->width;
                $height = $rectangle->height;

                if (($width < $minimumLength) || ($height < $minimumLength)) {

                    header("Location: ../index.php?calculation=The rectangle doesn't meet the minimal size requirements!");
                    exit();
                }

                $widthWirePillarSet = calculateWirePillarSet($width);
                $heightWirePillarSet = calculateWirePillarSet($height);

                $initialElementSet['wire'] =  2 * ($widthWirePillarSet['wire'] + $heightWirePillarSet['wire']) - $wires;
                $initialElementSet['pillar'] +=  2 * ($widthWirePillarSet['pillar'] + $heightWirePillarSet['pillar']);

                $price = $initialElementSet['corner'] * $settings['corner']['price']
                    + $initialElementSet['pillar'] * $settings['pillar']['price']
                    + $initialElementSet['wire'] * $settings['wire']['price']
                    + $initialElementSet['door'] * $settings['door']['price'];


                header("Location: ../index.php" .
                    "?pointC=" . implode(",", $pointC) .
                    "&pointD=" . implode(",", $pointD) .
                    "&perimeter={$perimeter}" .
                    "&area={$area}" .
                    "&price={$price}");
                exit();
            }
        }
    }
}

function calculateWirePillarSet($length)
{
    global $pillarLength, $wireLength, $minimumLength, $wirePillarLength;

    $fillableLength = $length - $minimumLength;
    $wirePillarSegments = $fillableLength / $wirePillarLength;

    $result = [
        'wire' => (int) $wirePillarSegments,
        'pillar' => (int) $wirePillarSegments,
        'remainingWire' => 0
    ];

    if (isWholeNumber($wirePillarSegments)) {
        return $result;
    }

    $remainingLength = fmod($fillableLength, $wirePillarLength);

    $result['wire'] += 1;

    if ($remainingLength <= $wireLength) {

        $result['remainingWire'] = round($wireLength - $remainingLength, 2);
    } else {

        $result['pillar'] += 1;
        $result['remainingWire'] = round($wireLength - ($remainingLength - $pillarLength), 2);
    }

    return $result;
}

function isWholeNumber($number)
{
    return abs($number - round($number)) < 0.0001;
}

function isGeoValid($value)
{
    $pattern = '/^(\-?([0-8]?[0-9](\.\d+)?|90(.[0]+)?)\s?[,]\s?)+(\-?([1]?[0-7]?[0-9](\.\d+)?|180((.[0]+)?)))$/';

    return preg_match($pattern, $value);
}

class RectangleArea
{
    protected $pointA;
    protected $pointB;
    protected $pointC;
    protected $pointD;

    public $width;
    public $height;

    protected $perimeter;
    protected $area;

    function __construct(array $pointA, array $pointB)
    {
        $this->pointA = $pointA;
        $this->pointB = $pointB;

        $this->pointC = [
            $pointA[0],
            $pointB[1]
        ];

        $this->pointD = [
            $pointB[0],
            $pointA[1]
        ];

        $this->width = round($this->getDistanceBetweenPoints($this->pointA, $this->pointC), 0);
        $this->height = round($this->getDistanceBetweenPoints($this->pointC, $this->pointB), 0);
    }

    public function getPointC()
    {
        return $this->pointC;
    }

    public function getPointD()
    {
        return $this->pointD;
    }

    public function getPerimeter()
    {
        return 2 * ($this->width + $this->height);
    }

    public function getArea()
    {
        return $this->width * $this->height;
    }

    protected function calculateWidthHeight()
    {
    }

    protected function getDistanceBetweenPoints(array $point1, array $point2)
    {
        $distance = sin(deg2rad($point1[0])) * sin(deg2rad($point2[0])) + cos(deg2rad($point1[0])) * cos(deg2rad($point2[0])) * cos(deg2rad($point1[1] - $point2[1]));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1853.15;

        return $distance;
    }
}
