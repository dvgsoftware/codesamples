<?php

/**
 * Add your routes here
 */
$app->get('/', function () use ($app) {
    echo $app['view']->getRender(null, 'index');
});

/**
 * Not found handler
 */
$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->getRender(null, '404');
});

/**
 * get all users
 */
$app->get('/user', function () use ($app) {
    $phql = "select * from User order by email";
    $users = $app->modelsManager->executeQuery($phql);
    $data = array();
    if (!empty($users)) {
        foreach ($users as $user) {
            $data[] = array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            );
        }
    }
    echo json_encode($data);
});

/**
 * search user by email
 */
$app->get('/user/search/{email}', function ($email) use ($app) {
    $phql = "select * from User where email like :email: order by email";
    $users = $app->modelsManager->executeQuery($phql, array(
        'email' => '%' . $email . '%'
    ));
    $data = array();
    if (!empty($users)) {
        foreach ($users as $user) {
            $data[] = array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            );
        }
    }
    echo json_encode($data);
});

/**
 * get user by primary key
 */
$app->get('/user/{id:[0-9]+}', function ($id) use ($app) {
    $phql = "select * from User where id = :id:";
    $user = $app->modelsManager->executeQuery($phql, array(
        'id' => $id
    ))->getFirst();

    // create a response
    $response = new Phalcon\Http\Response();
    if ($user == false) {
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
    } else {
        $response->setJsonContent(array(
            'status' => 'FOUND',
            'data' => array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            )
        ));
    }
    return $response;
});
// adds a new user
$app->post('/user', function () use ($app) {
    $user = $app->request->getJsonRawBody();

    $phql = "insert into User (name, email) values (:name:, :email:)";
    $status = $app->modelsManager->executeQuery($phql, array(
        'name' => $user->name,
        'email' => $user->email
    ));
    // create a response
    $response = new Phalcon\Http\Response();

    if ($status->success() == true) {
        $user->id = $status->getModel()->getId();
        $response->setJsonContent(array('status' => 'OK', 'data' => $user));
    } else {

        //Change the HTTP status
        $response->setStatusCode(500, "Internal Error");

        //Send errors to the client
        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }

    return $response;
});

// update user based on primary key
$app->put('/user/{id:[0-9]+}', function ($id) use ($app) {
    $user = $app->request->getJsonRawBody();
    $phql = "update User set name = :name:, email = :email: where id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id' => $id,
        'name' => $user->name,
        'email' => $user->email
    ));

    // create a response
    $response = new Phalcon\Http\Response();
    // check if update was success
    if ($status->success() == true) {
        $response->setJsonContent(array('status' => 'OK'));
    } else {
        //change the HTTP status
        $response->setStatusCode(500, "Internal Error");
        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));

    }

    return $response;
});

// delete user based on primary key
$app->delete('/user/{id:[0-9]+}', function ($id) use ($app) {
    $phql = "delete from User where id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array('id' => $id));

    //create a response
    $response = new Phalcon\Http\Response();

    if ($status->success() == true) {
        $response->setJsonContent(array('status' => 'OK'));
    } else {

        //Change the HTTP status
        $response->setStatusCode(500, "Internal Error");

        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }

    return $response;
});