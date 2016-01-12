<?php
require 'Slim/Slim.php';
require 'db.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
    'mode' => 'production',
	'log.writer' => new \Slim\Extras\Log\DateTimeFileWriter(array(
        'path' => './logs',
        'name_format' => 'Y-m-d',
        'message_format' => '%label% - %date% - %message%'
		))
));
    
$app->contentType("application/json");

$app->get('/getProvinsis', 'getProvinsis');
$app->get('/getKabupatens/:id', 'getKabupatens');
$app->get('/getKecamatans/:id', 'getKecamatans');
$app->get('/getDesas/:id', 'getDesas');

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
		'log.level' => \Slim\Log::WARN,
        'debug' => false
    ));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
		'log.level' => \Slim\Log::DEBUG,
        'debug' => true
    ));
});

$app->notFound(function () use ($app) {
   echo 'notFound coy';
});

$app->run();

//All Functions goes here
function getProvinsis() 
{
    global $app;

    $sql = "select * FROM provinsi ORDER BY id";
    try {
        $app->etag('getProvinsis');
        $db = getConnection();
        $stmt = $db->query($sql);
        $provinsis = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($provinsis);
    } catch(\PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getKabupatens($id) 
{
    $sql = "SELECT * FROM kabupaten WHERE id_prov = :id";
    
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $kabupatens = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($kabupatens);
    } catch (\PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getKecamatans($id) 
{
    $sql = "SELECT * FROM kecamatan WHERE id_kabupaten = :id";
    
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $kecamatans = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($kecamatans);
    } catch (\PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getDesas($id) 
{
    $sql = "SELECT * FROM desa WHERE id_kecamatan = :id";

    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $desas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($desas);
    } catch (\PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}