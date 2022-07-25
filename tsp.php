<?php

class TSP {

    private $locations     = array();        // all locations to visit
    private $longitudes = array();
    private $latitudes     = array();
    private $shortest_route = array();    // holds the shortest route
    private $shortest_routes = array();    // any matching shortest routes
    private $shortest_distance = 0;        // holds the shortest distance
    private $all_routes = array();        // array of all the possible combinations and there distances

    // add a location
    public function add($name,$longitude,$latitude){
        $this->locations[$name] = array('longitude'=>$longitude,'latitude'=>$latitude);
    }

    //LAT    LON     Location  - added method for parameter order
    public function _add($latitude,$longitude,$name){
        $this->locations[$name] = array('longitude'=>$longitude,'latitude'=>$latitude);
    }

    // the main function that des the calculations
    public function compute(){
        $locations = $this->locations;

        foreach ($locations as $location=>$coords){
            $this->longitudes[$location] = $coords['longitude'];
            $this->latitudes[$location] = $coords['latitude'];
        }
        $locations = array_keys($locations);

        $this->all_routes = $this->array_permutations($locations);

        $cache = array();
        foreach ($this->all_routes as $key=>$perms){
            $i=0;
            $total = 0;
            $n = count($this->locations)-1;
            foreach ($perms as $value){
                if ($i<$n){
                    $source = $perms[$i];
                    $dest = $perms[$i+1];
                    if(isset($cache[$source][$dest])){
                        $dist = $cache[$source][$dest];
                    } elseif (isset($cache[$dest][$source])) {
                        $dist = $cache[$dest][$source];
                    } else {
                        $dist = $this->distance($this->latitudes[$source],$this->longitudes[$source],$this->latitudes[$dest],$this->longitudes[$dest]);
                        $cache[$source][$dest] = $dist;
                    }
                    $total+=$dist;
                }
                $i++;
            }
            $this->all_routes[$key]['distance'] = $total;
            if ($total<$this->shortest_distance || $this->shortest_distance ==0){
                $this->shortest_distance = $total;
                $this->shortest_route = $perms;
                $this->shortest_routes = array();
            }
            if ($total == $this->shortest_distance){
                $this->shortest_routes[] = $perms;
            }
        }
    }

    // work out the distance between 2 longitude and latitude pairs
    function distance($lat1, $lon1, $lat2, $lon2) {
        if ($lat1 == $lat2 && $lon1 == $lon2) return 0;
        $theta = $lon1 - $lon2;
        $r_l1 = deg2rad($lat1);
        $r_l2 = deg2rad($lat2);
        $dist = sin($r_l1) * sin($r_l2) +  cos($r_l1) * cos($r_l2) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 69.09;
        return $miles;
    }

    // work out all the possible different permutations of an array of data
    private function array_permutations($items, $perms = array()){
        static $all_permutations;
        if (empty($items)) {
            $all_permutations[] = $perms;
        }  else {
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newitems = $items;
                $newperms = $perms;
                list($foo) = array_splice($newitems, $i, 1);
                array_unshift($newperms, $foo);
                $this->array_permutations($newitems, $newperms);
            }
        }
        return $all_permutations;
    }

    // return an array of the shortest possible route
    public function shortest_route(){
        return $this->shortest_route;
    }

    // returns an array of any routes that are exactly the same distance as the shortest (ie the shortest backwards normally)
    public function matching_shortest_routes(){
        return $this->shortest_routes;
    }

    // the shortest possible distance to travel
    public function shortest_distance(){
        return $this->shortest_distance;
    }

    // returns an array of all the possible routes
    public function routes(){
        return $this->all_routes;
    }
}

$s = microtime(true);

$tsp = new TSP;

//$tsp->_add(39.25,  106.30,  'Leadville,CO'); // 9th point (~30 seconds)
$tsp->_add(39.18,  103.70,  'Limon,CO');
$tsp->_add(38.50,  107.88,  'Montrose,CO');
$tsp->_add(38.28,  104.52,  'Pueblo,CO');
$tsp->_add(39.53,  107.80,  'Rifle,CO');
$tsp->_add(38.53,  106.05,  'Salida,CO');
$tsp->_add(40.48,  106.82,  'Steamboat Sp,CO');
$tsp->_add(37.25,  104.33,  'Trinidad,CO');
$tsp->_add(40.00,  105.87,  'Winter Park,CO');

$tsp->compute();

$e = microtime(true);
$t = $e - $s;
echo "Time: $t<br>";

echo "<pre>";
echo 'Shortest Distance: '.$tsp->shortest_distance();
echo '<br />Shortest Route: ';
print_r($tsp->shortest_route());
echo '<br />Num Routes: '.count($tsp->routes());
echo '<br />Matching shortest Routes: ';
print_r($tsp->matching_shortest_routes());
//echo '<br />All Routes: ';
//print_r($tsp->routes());