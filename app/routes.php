<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use SprintRunServer\Application\Conexao;



$app = new Silex\Application();

$conexao = Conexao::getInstance();


$app->get('sprints', function() use ($conexao)  {
  /*$sprints = [
    ['id' => 1, 'nome' => 'Sprint #1', 'obs' => 'Sprint id#1'],
    ['id' => 2, 'nome' => 'Sprint #2', 'obs' => 'Sprint id#2'],
    ['id' => 3, 'nome' => 'Sprint #3', 'obs' => 'Sprint id#3'],
    ['id' => 4, 'nome' => 'Sprint #4', 'obs' => 'Sprint id#4']
  ];*/
  
    $stmt = $conexao->prepare('SELECT * FROM sprints ORDER BY id DESC');
    $stmt->execute();
    $sprints = $stmt->fetchAll(PDO::FETCH_OBJ);

    if(!$sprints) return new JsonResponse($sprints, 404);

    return new JsonResponse($sprints, 200);
});

$app->get('points/{id_sprint}', function($id_sprint) use ($conexao){

    $stmt = $conexao->prepare('SELECT p.sprint_id, p.user_id, u.name, sum(pt.default_points) AS pontos 
                                FROM points p 
                                JOIN usuarios u JOIN points_types pt ON p.user_id = u.id AND p.point_type_id = pt.id
                                WHERE p.sprint_id=:id_sprint 
                                GROUP BY p.user_id, u.name, pt.default_points ORDER BY pontos DESC');
    
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
    
    try{

          $stmt = $conexao->prepare('INSERT INTO points(sprint_id, user_id, point_type_id, obs) VALUES(:sprints_id, :user_id, :point_type_id, :obs)');
          $stmt->bindParam(':sprints_id', $data['sprints_id'], \PDO::PARAM_INT);
          $stmt->bindParam(':user_id', $data['user_id'], \PDO::PARAM_INT);
          $stmt->bindParam(':point_type_id', $data['point_type_id'], \PDO::PARAM_INT);
          $stmt->bindParam(':obs', $data['obs'], \PDO::PARAM_STR);

          

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
