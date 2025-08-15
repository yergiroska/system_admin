<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

/**
 * Controlador responsable de gestionar la autenticación de los usuarios.
 *
 * Este controlador proporciona métodos para manejar el registro, inicio
 * de sesión y cierre de sesión de los usuarios. Incluye validaciones,
 * actualizaciones en la base de datos y redirecciones necesarias para
 * ofrecer una experiencia segura y eficiente.
 */
class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * @throws Throwable
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);
        DB::beginTransaction();
        try {

        $user = new User();
        $user->setName($request->name);
        $user->setEmail($request->email);
        $user->setPassword(Hash::make($request->password));
        // Actualiza la última sesión al momento actual en la zona horaria de Madrid
        $user->last_session = Carbon::now('Europe/Madrid');
        // Marca al usuario como conectado
        $user->is_connected = 1;
        // Guarda los cambios en la base de datos
        $user->save();

        $customer = new Customer();
        $customer->setUserId($user->id);   // asociación 1–1 con el usuario
        $customer->save();


        // Crea una nueva instancia de registro de inicio de sesión
        $user_login = new UserLogin();
        // Asigna el ID del usuario actualmente autenticado
        $user_login->user_id = $user->id;
        // Establece la hora de inicio de la conexión en la zona horaria de Madrid
        $user_login->start_connection = Carbon::now('Europe/Madrid');
        // Guarda el registro de inicio de sesión en la base de datos
        $user_login->save();


        DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput($request->only('name', 'email'))
                ->withErrors(['register' => 'No se pudo completar el registro. Inténtalo de nuevo.']);
        }

        Auth::login($user);
        return redirect()->route('dashboard');
    }

    /**
     * Maneja el inicio de sesión del usuario autenticado.
     *
     * Este método intenta autenticar al usuario con las credenciales
     * proporcionadas en la solicitud. Si la autenticación es exitosa,
     * actualiza la información del usuario en la base de datos, incluyendo
     * la fecha y hora de la última sesión y su estado de conexión.
     * También guarda un nuevo registro en la tabla de inicios de sesión
     * con la hora de inicio de la sesión.
     *
     * Si la autenticación falla, redirige de vuelta con un mensaje de error.
     *
     * @param Request $request La solicitud HTTP que contiene las credenciales de usuario.
     * @return RedirectResponse Redirige al dashboard en caso de éxito o regresa al formulario de inicio de sesión con errores.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            /*if ($request->hasCookie('last_logout_time')) {
                session(['last_logout_time' => $request->cookie('last_logout_time')]);
            }

            session(['last_login_time' => \Carbon\Carbon::now('Europe/Madrid')->format('d-m-Y H:i')]);
            */

            // Obtiene el usuario autenticado actualmente
            $user = User::find(Auth::id());
            // Actualiza la última sesión al momento actual en la zona horaria de Madrid
            $user->last_session = Carbon::now('Europe/Madrid');
            // Marca al usuario como conectado
            $user->is_connected = 1;
            // Guarda los cambios en la base de datos
            $user->save();


            // Crea una nueva instancia de registro de inicio de sesión
            $user_login = new UserLogin();
            // Asigna el ID del usuario actualmente autenticado
            $user_login->user_id = Auth::id();
            // Establece la hora de inicio de la conexión en la zona horaria de Madrid
            $user_login->start_connection = Carbon::now('Europe/Madrid');
            // Guarda el registro de inicio de sesión en la base de datos
            $user_login->save();

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function showLoginForm()
    {
        if (auth()->user()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Cierra la sesión del usuario actualmente autenticado.
     *
     * - Establece una cookie temporal que almacena la hora de cierre de sesión para un breve periodo.
     * - Actualiza los datos del usuario en la base de datos:
     *   - Define la última sesión del usuario en la zona horaria de Madrid.
     *   - Indica que el usuario está desconectado.
     * - Actualiza los detalles del último registro de inicio de sesión del usuario:
     *   - Define la hora de finalización de la conexión en la zona horaria de Madrid.
     *   - Guarda los cambios realizados.
     * - Realiza el cierre de sesión en el sistema de autenticación de Laravel.
     * - Redirige al formulario de inicio de sesión tras completar el proceso de cierre de sesión.
     *
     * @param Request $request La solicitud HTTP actual.
     * @return RedirectResponse Redirección a la página de inicio de sesión.
     */
    public function logout(Request $request)
    {
        //$logoutTime = \Carbon\Carbon::now('Europe/Madrid')->format('d-m-Y H:i');

        // Debug temporal
        //logger("Cerrando sesión, hora: " . $logoutTime);

        // Coloca en cookie temporal válida por una sola solicitud
        //cookie()->queue('last_logout_time', $logoutTime, 1); esta solo dura 1 minuto de vida

        //dura 30 días de vida
        //cookie()->queue('last_logout_time', $logoutTime, 43200); // 43200 minutos = 30 días

        // Obtiene el usuario actualmente autenticado
        $user = User::find(Auth::id());
        // Actualiza la última sesión del usuario al momento actual (zona horaria Madrid)
        $user->last_session = Carbon::now('Europe/Madrid');
        // Marca al usuario como desconectado (0)
        $user->is_connected = 0;
        // Guarda los cambios en la base de datos
        $user->save();

        // Obtiene el último registro de inicio de sesión del usuario actual
        $user_login = UserLogin::where('user_id', Auth::id())->orderBy('id', 'desc')->first();
        // Actualiza el ID del usuario asociado al registro
        $user_login->user_id = Auth::id();
        // Establece la hora de finalización de la conexión en la zona horaria de Madrid
        $user_login->end_connection = Carbon::now('Europe/Madrid');
        // Guarda los cambios en la base de datos
        $user_login->save();


        Auth::logout();

        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
