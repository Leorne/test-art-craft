<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Http\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UserController extends Controller
{
    public function index()
    {
        $request = Request::createFromGlobals();

        $querySort = $request->query->has('sort') ? $request->query->get('sort') : null;
        $queryOrder = $request->query->has('order') ? $request->query->get('order') : null;
        $sort = 'name';
        $order = 'desc';
        if (($querySort === 'name') || ($querySort === 'email')) {
            $sort = $querySort;
            if (($queryOrder === 'desc') || ($queryOrder === 'asc')) {
                $order = $queryOrder;
            }
        }
        $users = User::orderBy($sort, $order)->get();
        $newNameOrder = (($order === 'desc') && ($sort === 'name')) ? 'asc' : 'desc';
        $newEmailOrder = (($order === 'desc') && ($sort === 'email')) ? 'asc' : 'desc';
        return $this->render('index.html.twig', [
            'users' => $users,
            'nameOrder' => $newNameOrder,
            'emailOrder' => $newEmailOrder,
        ]);
    }

    public function show($key)
    {
        try {
            $user = User::where('key', '=', $key)->firstOrFail();
            return $this->render('show.html.twig', [
                'user' => $user
            ]);
        } catch (ModelNotFoundException $e) {
            throw new ResourceNotFoundException();
        }
    }

    public function create()
    {
        $captcha = $this->container->get('captcha');
        $captchaPhrase = $captcha->getPhrase();
        $session = $this->container->get('session');
        $session->getFlashBag()->set('captcha', $captchaPhrase);
        $csrf = $this->container->get('csrf')->getToken();
        return $this->render('register.html.twig', [
            'csrf_token' => $csrf
        ]);
    }

    public function store()
    {
        $request = $this->request;

        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $photo = $request->files->get('photo');
        $session = $this->container->get('session');
        $captchaInput = mb_strtolower($request->request->get('captcha'));
        $captchaSession = mb_strtolower($session->getFlashBag()->get('captcha')[0]);

        $validator = new Validator();
        if ($errors = @$validator->validate([
            $name => 'name',
            $email => 'email',
            $photo => 'image'
        ])) {
            $session->getFlashBag()->setAll([
                'errors' => $errors,
                'name' => $name,
                'email' => $email
            ]);
            return $this->redirect('/register');
        }
        if ($captchaInput !== $captchaSession){
            $session->getFlashBag()->setAll([
                'name' => $name,
                'email' => $email,
                'errors' => [
                    'captcha' => ['Invalid captcha!']
                ]
            ]);
            return $this->redirect('/register');
        }

        $photoName = uniqid($name) . ".{$photo->getClientOriginalExtension()}";
        $photo->move(getcwd() . '/uploads', $photoName);
        $key = md5(uniqid($email));

        User::create([
            'name' => $name,
            'email' => $email,
            'photo' => $photoName,
            'key' => $key,
        ]);

        $response = new RedirectResponse('/');
        $response->headers->setCookie(Cookie::create('user', $key, strtotime('+1 year')));
        return $response;
    }
}