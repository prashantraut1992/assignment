<?php
 
    //Home page redirection
    $app->get('/', function ($request, $response, $args) {        
        $response = $this->renderer->render($response, 'index.phtml', ['content' => 'home']);
        return $response;
    });
    
    // get add user form dropdown & related data on form load
    $app->get('/add-user-form', function ($request, $response, $args) {  
        $response = $this->renderer->render($response, 'index.phtml', ['content' => 'form','formtype' => 'Register']);
        return $response;
    });

    //GET users list 
    $app->get('/users-list', function ($request, $response, $args) {
        $userslist = array();

        try{
        $sqlS = "SELECT `user_id`, `full_name`, `email`, `user_name`,`date_added`, `date_modified`, `is_deleted` FROM `site_users`";
        $row = $this->db->prepare($sqlS);        
        $row->execute();                
        $userslist = $row->fetchAll();
        $response = $this->renderer->render($response, 'index.phtml', ['content' => 'list','userslist' => $userslist]);
        return $response;
        
        }catch(PDOException $e) {
            $response = $this->renderer->render($response, 'index.phtml', ['content' => 'list','userslist' => $userslist]);
            return $response;          

        }

    });
    //END - GET users list 
    
    //Check subject already exists or not
    $app->post('/check-user-exist', function ($request, $response, $args) {
        
        $postData = $request->getParsedBody(); 
        
        $user_name = trim($postData['user_name']);        
        
        $user_name =  preg_replace('/[^A-Za-z0-9\-]/', ' ', $user_name); // Removes special chars.
        
        $rerunArr['status'] = "ERR";
        try{

            $sqlS = "SELECT COUNT(`user_id`) as count FROM `site_users` WHERE `user_name` like '$user_name' ORDER BY user_id";
            $row = $this->db->prepare($sqlS);
            $row->execute();
            $userlist = $row->fetchAll();
            
            if(isset($userlist[0]) && isset($userlist[0]['count']) && $userlist[0]['count'] > 0 ) 
            {
                $rerunArr['status'] = "ERR";

            } else {
                $rerunArr['status'] = "OK";
                //$rerunArr['count'] = $userlist;
            }

        }catch(PDOException $e) {
             // show error details 
             $rerunArr['status'] = $e->getMessage();             
        }

        return $this->response->withJson($rerunArr);

    });
    //END - Check subject already exists or not
    
     // Delete users record 
     $app->post('/delete-user', function ($request, $response, $args) {
        $ids = $request->getParsedBody(); 
        $rerunArr['status'] = "ERR";

        try{
            if(!isset($ids['userids']) || $ids['userids'] == '' || empty($ids['userids'])) {
                $rerunArr['status'] = "ERR";
                return $this->response->withJson($rerunArr);
            }

            $userids = $ids['userids'];
            $sqlU = "UPDATE `site_users` SET is_deleted = 1  WHERE `user_id` IN ( $userids )";
            $query = $this->db->prepare($sqlU);
            $query->execute();          
            $rerunArr['status'] = "OK";

        }catch(PDOException $e) {
            // show error details 
            $rerunArr['status'] = $e->getMessage();            
        }
       
        return $this->response->withJson($rerunArr);

    });
    //END - Delete users record     
     
      // Delete users record 
     $app->post('/activate-user', function ($request, $response, $args) {
        $ids = $request->getParsedBody(); 
        $rerunArr['status'] = "ERR";

        try{
            if(!isset($ids['userids']) || $ids['userids'] == '' || empty($ids['userids'])) {
                $rerunArr['status'] = "ERR";
                return $this->response->withJson($rerunArr);
            }

            $userids = $ids['userids'];
            $sqlU = "UPDATE `site_users` SET is_deleted = 0  WHERE `user_id` IN ( $userids )";
            $query = $this->db->prepare($sqlU);
            $query->execute();          
            $rerunArr['status'] = "OK";

        }catch(PDOException $e) {
            // show error details 
            $rerunArr['status'] = $e->getMessage();            
        }
       
        return $this->response->withJson($rerunArr);

    });
    //END - Delete users record  



    //POST - Add new user 
    $app->post('/add-user', function ($request, $response, $args) {
        $postdata = $request->getParsedBody();  

        $rerunArr['status'] = "ERR";
        
        if(!isset($postdata) || !is_array($postdata)) 
        {
            return $rerunArr['status'];
        }

        $full_name = isset($postdata['full_name']) ? trim($postdata['full_name']):'';
        $email = isset($postdata['email']) ? trim($postdata['email']):'';
        $user_name = isset($postdata['user_name']) ? trim($postdata['user_name']):'';
        $password = isset($postdata['password']) ? trim($postdata['password']):'';

    try{

        $sqlI = "INSERT INTO site_users(user_name, password, full_name, email, date_added) VALUES ('$user_name','md5($password)', '$full_name','$email',NOW())";
        $query = $this->db->prepare($sqlI);

        $query->execute();          
        $rerunArr['status'] = "OK";

        }catch(PDOException $e) {
            // show error details 
            $rerunArr['status'] = $e->getMessage();            
        }

        return $this->response->withJson($rerunArr);

    });



    // GET - all user details users record 
    $app->post('/edit-user', function ($request, $response, $args) {
        $ids = $request->getParsedBody(); 
        $rerunArr['status'] = "ERR";

        try{
            if(!isset($ids['user_id']) || $ids['user_id'] == '' || empty($ids['user_id'])) {
                $rerunArr['status'] = "ERR";
                return $this->response->withJson($rerunArr);
            }

            $userid = $ids['user_id'];

            $sqlS = "SELECT `user_id`, `full_name`, `email`, `user_name`,`date_added`, `date_modified`, `is_deleted` FROM `site_users`   
                WHERE `is_deleted` = 0 AND user_id = $userid ";
                
            $row = $this->db->prepare($sqlS);
            $row->execute();
            $userdata = $row->fetchAll();
            if(count($userdata) > 0) {
                $rerunArr['userdata'] = $userdata;
                $rerunArr['status'] = "OK";
            }
            

        }catch(PDOException $e) {
            // show error details 
            $rerunArr['status'] = $e->getMessage();            
        }

        return $this->response->withJson($rerunArr);

    });
    //END - get all user details  

        //GET issue form related data
    $app->get('/edit-user-form', function($request, $response, $args) {
        $editissuedata = array();        
        $response = $this->renderer->render($response, 'index.phtml', ['content' => 'form','formtype' => 'Edit','editissuedata'=>$editissuedata]);
        return $response;

     });


    //update new user 
    $app->post('/update-user', function ($request, $response, $args) {
        $postdata = $request->getParsedBody();  

        $rerunArr['status'] = "ERR";
        
        if(!isset($postdata) || !is_array($postdata)) 
        {
            return $rerunArr['status'];
        }
        $user_id = isset($postdata['user_id']) ? trim($postdata['user_id']):'';
        $subject = isset($postdata['subject']) ? trim($postdata['subject']):'';
        $description = isset($postdata['description']) ? trim($postdata['description']):'';
        $status_id = isset($postdata['status_id']) ? trim($postdata['status_id']):'';
        $priority_id = isset($postdata['priority_id']) ? trim($postdata['priority_id']):'';
        $region_ids = isset($postdata['region_id']) ? trim(implode(',',$postdata['region_id'])):'';
        $due_date = isset($postdata['due_date']) ? date("Y-m-d h:i:s", strtotime(trim($postdata['due_date']))):'';
        $assignee_id = isset($postdata['assignee_id']) ? trim($postdata['assignee_id']):'';
        $reviewer_id = isset($postdata['reviewer_id']) ? trim($postdata['reviewer_id']):'';
        $target_version_id = isset($postdata['target_version_id']) ? trim($postdata['target_version_id']):'';
        $image_name = isset($postdata['image_name']) ? trim($postdata['image_name']):'';
        $reviewer_comments = isset($postdata['reviwer_comments']) ? trim($postdata['reviwer_comments']):'';

        try{
            
        $sqlU = "UPDATE `users` SET `subject`= '$subject',`description`='$description',`status_id`='$status_id',`priority_id`='$priority_id',`region_ids`='$region_ids',`due_date`='$due_date',`assignee_id`=$assignee_id,`reviewer_id`=$reviewer_id,`target_version_id`=$target_version_id,`image_name`='$image_name',`reviewer_comments`='$reviewer_comments',`date_modified`=NOW() WHERE user_id = $user_id";
        $query = $this->db->prepare($sqlU);

        $query->execute();          
        $rerunArr['status'] = "OK";

        }catch(PDOException $e) {
            // show error details 
            $rerunArr['status'] = $e->getMessage();            
        }

        return $this->response->withJson($rerunArr);

    });