<?php

namespace app\models;

class Users extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'users';
    }    
    
    public function findByUsername($username, $password)
    {
                
        $query = Users::find()
        ->where(['username' => $username])
        ->one();

        if($query){

            $senhaCad = $query->password;
            $senhaEnv = hash('sha256', $password);
            
            if($senhaCad == $senhaEnv)
            {                
                return $query->id;
            }
        }      

        return 0;
    }

}
