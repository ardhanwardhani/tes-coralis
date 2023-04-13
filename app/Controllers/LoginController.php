<?php

namespace App\Controllers;

use App\Models\UserModel;

class LoginController extends BaseController
{
    protected $model;
    protected $session;

    public function __construct()
    {
        $this->model = new UserModel();
        $this->helpers = ['form', 'url'];
        $this->session = session();
    }

    public function index()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Login'
        ];

        return view('auth/login', $data);
    }

    private function isLoggedIn(): bool
    {
        if (session()->get('logged_in')) {
            return true;
        }

        return false;
    }

    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $credentials = ['email' => $email];

        $user = $this->model->where($credentials)->first();

        if (! $user) {
            session()->setFlashdata('error', 'Email anda salah.');
            return redirect()->back();
        }

        $passwordCheck = password_verify($password, $user['password']);

        if (! $passwordCheck) {
            session()->setFlashdata('error', 'Password anda salah.');
            return redirect()->back();
        }

        $userData = [
            'name' => $user['name'],
            'email' => $user['email'],
            'image' => $user['image'],
            'logged_in' => TRUE
        ];

        $this->session->set($userData);

        $data = [
            'title' => 'dashboard',
            'name' => $this->session->name,
            'email' => $this->session->email,
            'image' => $this->session->image
        ];
        return view('dashboard', $data);
    }

    public function forgot_password(){
        $data = [
            'title' => 'Lupa Password'
        ];
        if($this->request->getMethod()=='post'){
             $rules = [
                'email'=>[
                    'label' => 'Email',
                    'rules'=> 'required|valid_email',
                    'errors' => [
                        'required' =>'{field} field required',
                        'valid_email' => 'Valid {field} required'
                    ]
                ],
            ];
             if($this->validate($rules)){
                 $email = $this->request->getVar('email',FILTER_SANITIZE_EMAIL);
                 $userdata = $this->model->verifyEmail($email);
                 if(!empty($userdata)){
                     if($this->model->updatedAt($userdata['uniid'])){
                        $to = $email;
                        $subject = 'Reset Password Link';
                        $token = $userdata['uniid'];
                        $message = 'Hi '.$userdata['name'].'<br><br>'
                                . 'Your reset password request has been received. Please click'
                                . 'the below link to reset your password.<br><br>'
                                . '<a href="'. base_url().'login/reset_password/'.$token.'">Click here to Reset Password</a><br><br>'
                                . 'Thanks<br>Anugrah';
                        $email = \Config\Services::email();
                        $email->setTo($to);
                        $email->setFrom('anugrahw100500@gmail.com','Anugrah Wardhani');
                        $email->setSubject($subject);
                        $email->setMessage($message);
                        if($email->send())
                        {
                            session()->setFlashdata('success','Reset password link sent to your registred email. Please verify with in 15mins');
                            return redirect()->to(current_url());
                        }
                        else
                        {
                            $data = $email->printDebugger(['headers']);
                            print_r($data);
                        }
                     }
                     else
                     {
                         $this->session->setFlashdata('error','Sorry! Unable yo update. try again');
                        return redirect()->to(current_url());
                     }
                 }
                 else{
                    $this->session->setFlashdata('error','Email does not exists',3);
                    return redirect()->to(current_url());
                 }
             }
             else{
                 $data['validation']=$this->validator;
             }
        }
        return view("auth/forgotpass",$data);
    }
    

    public function reset_password($token=null){
        $data = [];
        if(!empty($token)){
            $userdata = $this->model->verifyToken($token);
            if(!empty($userdata)){
                if($this->checkExpiryDate($userdata['updated_at'])){
                    if($this->request->getMethod()=='post'){
                       $rules = [
                           'password' => [
                               'label' =>'Password',
                               'rules' => 'required|min_length[6]|max_length[16]',
                           ],
                           'cpassword' => [
                               'label' => 'Confirm Password',
                               'rules' => 'required|matches[password]'
                           ],
                       ];
                       if($this->validate($rules)){
                           $pwd = password_hash($this->request->getVar('password'),PASSWORD_DEFAULT);
                           if($this->model->updatePassword($token,$pwd)){
                               session()->setFlashdata('success','Password updated successfully, Login now');
                               return redirect()->to(base_url().'/login');
                           }
                           else{
                               session()->setFlashdata('error','Sorry! Unablr to update Password. try again');
                               return redirect()->to(current_url());
                           }
                       }
                       else{
                           $data['validation'] = $this->validator;
                       }
                   }
                }
                else
                {
                    $data['error'] = 'Reset password link was expired.';
                }
            }
            else
            {
                $data['error'] = 'Unable to find user account';
            }
        }
        else{
            $data['error'] = 'Sorry! Unauthourized access';
        }
        $data['title'] = 'Reset Password';
        return view('auth/resetpassword', $data);
         
    }

    public function checkExpiryDate($time){
        $update_time = strtotime($time);
        $current_time = time();
        $timeDiff = ($current_time - $update_time)/60;
        if($timeDiff < 900){
            return true;
        }
        else
        {
            return false;
        }
    }

}