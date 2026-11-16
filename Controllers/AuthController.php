<?php
// Controllers/AuthController.php
// Login dan Logout

require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController {

    // Dialog "Gagal Login" dengan tampilan khusus (popup membulat), lalu kembali ke form
    private function swalLoginGagal() {
        echo "<style>
            .custom-swal-popup {
                font-family: 'Plus Jakarta Sans', sans-serif !important;
                border-radius: 20px !important;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
                padding: 2em !important;
            }
            .custom-swal-title {
                font-weight: 800 !important;
            }
            .custom-swal-button {
                border-radius: 50px !important;
                padding: 12px 32px !important;
                font-weight: 700 !important;
            }
        </style>";

        $this->swal([
            'title'              => 'Gagal Login',
            'text'               => 'Username atau Password salah!',
            'icon'               => 'error',
            'iconColor'          => '#e74c3c',
            'confirmButtonText'  => 'Coba Lagi',
            'confirmButtonColor' => '#e74c3c',
            'background'         => '#ffffff',
            'color'              => '#0f3832',
            'customClass'        => [
                'popup'         => 'custom-swal-popup',
                'title'         => 'custom-swal-title',
                'confirmButton' => 'custom-swal-button'
            ]
        ], null, null, true);
    }

    // ------------------------------------------------------------------
    // LOGIN  (GET = tampilkan form, POST = proses)
    // ------------------------------------------------------------------
    public function login() {
        if (!$this->isPost()) {
            $this->render('Login/login');
            return;
        }

        $username = $this->post('username');
        $password = $this->post('password');

        // Akun yang dicari hanya akun aktif (lihat m_user::login)
        $user = $this->model('m_user')->login($username);

        // Password boleh hasil hash atau teks biasa (toleransi data lama/testing)
        $passwordValid = $user && (password_verify($password, $user['password']) || $password === $user['password']);

        if (!$passwordValid) {
            $this->swalLoginGagal();
        }

        // Simpan sesi login
        $_SESSION['data'] = [
            'id_user'      => $user['id_user'],
            'nama_lengkap' => $user['nama_lengkap'],
            'username'     => $user['username'],
            'role'         => $user['role'],
            'status_aktif' => $user['status_aktif']
        ];

        $this->model('m_log')->catat($user['id_user'], "Pengguna login sebagai " . strtoupper($user['role']));

        // Arahkan ke dashboard sesuai role
        if (in_array($user['role'], ['admin', 'petugas', 'owner'], true)) {
            $this->redirect($user['role'], 'dashboard');
        }

        echo "<script>alert('Role tidak terdefinisi.'); window.location='index.php'</script>";
        exit();
    }

    // ------------------------------------------------------------------
    // LOGOUT
    // ------------------------------------------------------------------
    public function logout() {
        if (!empty($_SESSION['data']['id_user'])) {
            $this->model('m_log')->catat($_SESSION['data']['id_user'], "Pengguna logout dari sistem");
        }

        // Hapus data sesi dan cookie-nya
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        $this->redirect('auth', 'login');
    }
}
