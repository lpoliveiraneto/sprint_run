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

$app->get('points', function() {
  $points = [
    ['id' => 1, 'nome' => 'Felipe', 'points' => 12],
    ['id' => 2, 'nome' => 'Guilherme', 'points' => 10],
    ['id' => 3, 'nome' => 'Lucas', 'points' => 8],
    ['id' => 4, 'nome' => 'Pedro', 'points' => 5]
  ];

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
