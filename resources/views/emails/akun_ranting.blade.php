<!DOCTYPE html>
<html>

<head>
    <title>Akun SIM PAC</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f6f9; padding: 20px;">

    <div
        style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-top: 4px solid #28a745; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">

        <h2 style="color: #28a745; text-align: center; margin-top: 0;">Selamat Bergabung!</h2>

        <p>Halo Rekan/Rekanita <strong>{{ $nama_pengurus }}</strong>,</p>

        <p>Kabar gembira! Pengajuan rekomendasi pengesahan untuk <strong>{{ $nama_organisasi }}</strong> telah
            <strong>DISETUJUI</strong> oleh Admin PAC.</p>

        <p>Dengan disahkannya kepengurusan ini, Anda kini memiliki akses ke dalam Sistem Informasi Manajemen (SIM PAC).
            Berikut adalah informasi kredensial untuk masuk ke dasbor Anda:</p>

        <div
            style="background-color: #f8f9fa; padding: 15px 20px; border-left: 4px solid #17a2b8; margin: 20px 0; border-radius: 3px;">
            <p style="margin: 0 0 10px 0;"><strong>URL Akses Sistem:</strong> <a href="{{ route('login') }}"
                    style="color: #17a2b8; text-decoration: none;">{{ route('login') }}</a></p>
            <p style="margin: 0 0 10px 0;"><strong>Email (Username):</strong> {{ $email_login }}</p>
            <p style="margin: 0;"><strong>Password:</strong> <span
                    style="background: #e9ecef; padding: 2px 5px; border-radius: 3px; font-family: monospace;">{{ $password_login }}</span>
            </p>
        </div>

        <p style="color: #d9534f; font-size: 13px;"><em>*Peringatan: Demi keamanan, harap segera mengubah password Anda
                melalui menu pengaturan profil setelah berhasil login.</em></p>

        <hr style="border: none; border-bottom: 1px solid #eeeeee; margin: 25px 0;">

        <p style="font-size: 14px; color: #6c757d; text-align: center; margin: 0;">
            Salam Belajar, Berjuang, Bertaqwa!<br>
            <strong>Admin PAC IPNU IPPNU</strong>
        </p>

    </div>
</body>

</html>
