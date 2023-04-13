<?php namespace App\Controllers;

use CodeIgniter\Files\File;
use App\Models\UserModel;

class RegisterController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new UserModel();
        $this->helpers = ['form', 'url'];
    }

    public function index()
    {
        $data = [
            'title' => 'Register'
        ];

        return view('auth/register', $data);
    }

    public function store()
    {
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $uniid = md5(str_shuffle('abcsefghijklmonpqrtuvwxyz'.time()));
        $image = $this->request->getFile('image');
        $newName = $image->getRandomName();

        $user = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'image' => $newName,
            'uniid' => $uniid
        ];

        $save = $this->model->save($user);

        if ($save && $image->isValid() && !$image->hasMoved()) {
            $image->move(ROOTPATH . 'public/uploads/images/', $newName);
            session()->setFlashdata('success', 'Register Berhasil!');
            return redirect()->to(base_url('login'));
        } else {
            session()->setFlashdata('error', $this->model->errors());
            return redirect()->back();
        }
    }
}