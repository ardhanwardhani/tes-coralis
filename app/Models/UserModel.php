<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = ['name', 'email', 'password', 'image', 'uniid'];
    protected $useTimeStamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]',
        'image' => 'uploaded[image]|is_image[image]|max_size[image,2048]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Maaf, email sudah digunakan. Silakan gunakan email yang lain.'
        ],
        'password' => [
            'min_length' => 'Password harus mengandung 8 karakter'
        ],
        'image' => [
            'uploaded' => 'Silakan masukan gambar',
            'mime_in' => 'File berupa jpg, jpeg, gif, dan png.',
            'max_size' => 'File maks. 2 MB'
        ],
    ];

    protected $skipValidation = false;
    protected $beforeInsert = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    public function verifyEmail($email){
       
        $builder = $this->db->table('users');
        $builder->select("uniid,name,password");
        $builder->where('email',$email);
        $result = $builder->get();
        if(count($result->getResultArray())==1)
        {
            return $result->getRowArray();
        }
        else
        {
            return false;
        }
    }

    public function updatedAt($id){
        $builder = $this->db->table('users');
        $builder->where('uniid', $id);
        $builder->update(['updated_at' => date('Y-m-d H:i:s')]);
        if($this->db->affectedRows()==1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function verifyToken($token){
        $builder = $this->db->table('users');
        $builder->select("uniid,name,updated_at");
        $builder->where('uniid', $token);
        $result = $builder->get();
        if(count($result->getResultArray())==1)
        {
            return $result->getRowArray();
        }
        else
        {
            return false;
        }
    }
    public function updatePassword($id,$pwd){
        $builder = $this->db->table('users');
        $builder->where('uniid', $id);
        $builder->update(['password'=>$pwd]);
        if($this->db->affectedRows()==1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}