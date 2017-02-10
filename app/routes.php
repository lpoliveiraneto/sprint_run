<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use SprintRunServer\Application\Conexao;



$app = new Silex\Application();

 /*com doctrine
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'Sprints_Points',
            'user' => 'root',
            'password' => 'leonidesNETO',
            'charset' => 'utf8mb4'
          
      ),
  ));



$app->get('sprints', function() use($app) {
  $sql= 'SELECT * FROM sprints ORDER BY id DESC';

  $sprints = $app['db']->fetchAll($sql);

  if(!$sprints) new JsonResponse($sprints, 400);

  return new JsonResponse($sprints,200);
});

$app->get('sprints/{id_sprint}', function($id_sprint) use ($app){

  $sql = "SELECT p.sprint_id, p.user_id, u.name, sum(pt.default_points) AS pontos 
    FROM points p 
    JOIN usuarios u JOIN points_types pt ON p.user_id = u.id AND p.point_type_id = pt.id
    WHERE p.sprint_id=? 
    GROUP BY p.user_id, u.name, pt.default_points ORDER BY pontos DESC";

    //$pontos = $app['db']->fetchAll($sql, array((int) $id_sprint));
    if($pontos = $app['db']->fetchAll($sql, array((int) $id_sprint))){
      return new JsonResponse($pontos, 200);
    }else{
      return new JsonResponse('ERRO', 400);
    }


});

$app->post('points', function(Request $request) use ($app){

    $app['db']->beginTransaction();
    $data = $request->request->all();

    try{

          $stmt = $app['db']->prepare('INSERT INTO points(sprint_id, user_id, point_type_id, obs) VALUES(:sprints_id, :user_id, :point_type_id, :obs)');
          $stmt->bindParam(':sprints_id', $data['sprints_id'], \PDO::PARAM_INT);
          $stmt->bindParam(':user_id', $data['user_id'], \PDO::PARAM_INT);
          $stmt->bindParam(':point_type_id', $data['point_type_id'], \PDO::PARAM_INT);
          $stmt->bindParam(':obs', $data['obs'], \PDO::PARAM_STR);

          

          if(!$stmt->execute() === false){
            $app['db']->commit();
            $status = new JsonResponse('Sucesso', 200);
          }else{
            $app['db']->rollBack();
            $status = new JsonResponse('Erro', 400);
            
          }

    }catch(PDOException $e){
          //echo $e->getMessage();
          $app['db']->rollBack();
    }

    return $status;




});
*/
//COM PDO
$conexao = Conexao::getInstance();


$app->get('sprints', function() use ($conexao)  {
  
    $stmt = $conexao->prepare('SELECT id,name,obs FROM sprints ORDER BY id DESC');
    $stmt->execute();
    
    $sprints = $stmt->fetchAll(PDO::FETCH_OBJ);

    if(!$sprints) return new JsonResponse($sprints, 404);

    return new JsonResponse($sprints, 200);
});


$app->get('points/{id_sprint}', function($id_sprint) use ($conexao){

    $stmt = $conexao->prepare('SELECT s.sprint_id, s.user_id, u.name, sum(pt.default_points) AS pontos 
                                FROM scores s 
                                JOIN users u JOIN points_types pt ON s.user_id = u.id AND s.point_type_id = pt.id
                                WHERE s.sprint_id=:id_sprint 
                                GROUP BY s.user_id ORDER BY pontos DESC');
    
    $stmt->bindParam(':id_sprint', $id_sprint);

    if(!$stmt->execute() === false){
        $pontos = $stmt->fetchAll(PDO::FETCH_OBJ);
        $status = new JsonResponse($pontos, 200);
    }else{
        $status = new JsonResponse('Erro', 400);
    }

    return $status;

  return new JsonResponse($points, 200);
});


$app->post('points', function(Request $request) use ($app, $conexao){

    $conexao->beginTransaction();
    $data = $request->request->all();
    $created_at = date('Y-m-d H:m:s');
    
    try{

          $stmt = $conexao->prepare('INSERT INTO scores(sprint_id, user_id, point_type_id, obs, created_at, updated_at) VALUES(:sprints_id, :user_id, :point_type_id, :obs, :created_at, :updated_at)');
          $stmt->bindParam(':sprints_id', $data['sprints_id'], \PDO::PARAM_INT);
          $stmt->bindParam(':user_id', $data['user_id'], \PDO::PARAM_INT);
          $stmt->bindParam(':point_type_id', $data['point_type_id'], \PDO::PARAM_INT);
          $stmt->bindParam(':obs', $data['obs'], \PDO::PARAM_STR);
          $stmt->bindParam(':created_at', $created_at, \PDO::PARAM_STR);
          $stmt->bindParam(':updated_at', date('Y-m-d H:m:s'), \PDO::PARAM_STR);

          if(!$stmt->execute() === false){
            $conexao->commit();
            $status = new JsonResponse('Sucesso', 200);
          }else{
            $conexao->rollBack();
            $status = new JsonResponse('Erro', 400);

            
          }

    }catch(PDOException $e){
          //echo $e->getMessage();
          $conexao->rollBack();
    }

    return $status;

});


$app['debug'] = true;

$app->run();
