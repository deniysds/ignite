<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Kirim email menggunakan PHPMailer dengan konfigurasi yang sama seperti EmailController
     */
    public function sendEmail(array $data)
    {
        $mail = new PHPMailer(true);

        try {
            // Konfigurasi SMTP persis sama dengan EmailController yang berhasil
            $mail->isSMTP();
            $mail->Host = $this->config['host'] ?? 'mail.ag.co.id';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'] ?? '';
            $mail->Password = $this->config['password'] ?? '';

            // Ini adalah kunci utama: ENCRYPTION_STARTTLS dengan port 465
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['port'] ?? 465;

            // Bypass SSL Verification persis sama dengan yang berhasil
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Set pengirim
            $mail->setFrom(
                $data['from']['address'] ?? '',
                $data['from']['name'] ?? 'Notifikasi Sistem Laravel'
            );

            // Set penerima
            foreach ($data['to'] as $recipient) {
                $mail->addAddress($recipient['address'], $recipient['name'] ?? '');
            }

            // Set CC jika ada
            if (isset($data['cc'])) {
                foreach ($data['cc'] as $cc) {
                    $mail->addCC($cc['address'], $cc['name'] ?? '');
                }
            }

            // Set BCC jika ada
            if (isset($data['bcc'])) {
                foreach ($data['bcc'] as $bcc) {
                    $mail->addBCC($bcc['address'], $bcc['name'] ?? '');
                }
            }

            // Set subject
            $mail->Subject = $data['subject'] ?? 'Notifikasi Sistem';

            // Set konten
            if (isset($data['html'])) {
                $mail->isHTML(true);
                $mail->Body = $data['html'];
                $mail->AltBody = $data['text'] ?? strip_tags($data['html']);
            } else {
                $mail->Body = $data['text'] ?? '';
                $mail->AltBody = $data['text'] ?? '';
            }

            // Tambahkan attachment jika ada
            if (isset($data['attachments']) && is_array($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    if (isset($attachment['path']) && file_exists($attachment['path'])) {
                        $attachmentName = $attachment['name'] ?? basename($attachment['path']);
                        $mail->addAttachment($attachment['path'], $attachmentName);
                    }
                }
            }

            // Kirim email
            $mail->send();

            return [
                'success' => true,
                'message' => 'Email berhasil dikirim menggunakan PHPMailer'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengirim email dengan PHPMailer',
                'error' => $mail->ErrorInfo
            ];
        }
    }

    /**
     * Konversi data dari Laravel Mail ke format PHPMailer
     */
    public function convertFromLaravelMail($laravelMail)
    {
        $data = [
            'from' => [
                'address' => $laravelMail->from[0]['address'] ?? 'BAGI@ag.co.id',
                'name' => $laravelMail->from[0]['name'] ?? 'Notifikasi Sistem Laravel'
            ],
            'to' => [],
            'cc' => [],
            'bcc' => [],
            'subject' => $laravelMail->subject ?? 'Notifikasi Sistem',
            'html' => $laravelMail->html ?? null,
            'text' => $laravelMail->text ?? null
        ];

        // Konversi penerima
        foreach ($laravelMail->to as $recipient) {
            $data['to'][] = [
                'address' => $recipient['address'],
                'name' => $recipient['name'] ?? ''
            ];
        }

        // Konversi CC
        if (isset($laravelMail->cc)) {
            foreach ($laravelMail->cc as $cc) {
                $data['cc'][] = [
                    'address' => $cc['address'],
                    'name' => $cc['name'] ?? ''
                ];
            }
        }

        // Konversi BCC
        if (isset($laravelMail->bcc)) {
            foreach ($laravelMail->bcc as $bcc) {
                $data['bcc'][] = [
                    'address' => $bcc['address'],
                    'name' => $bcc['name'] ?? ''
                ];
            }
        }

        // Konversi attachments
        if (isset($laravelMail->attachments) && is_array($laravelMail->attachments)) {
            $data['attachments'] = [];
            foreach ($laravelMail->attachments as $attachment) {
                if (isset($attachment['file'])) {
                    $data['attachments'][] = [
                        'path' => $attachment['file'],
                        'name' => $attachment['options']['as'] ?? basename($attachment['file']),
                        'mime' => $attachment['options']['mime'] ?? null
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * Kirim email dengan attachment - method helper yang lebih sederhana
     *
     * @param string $to Alamat email tujuan
     * @param string $subject Subjek email
     * @param string $body Isi email (HTML)
     * @param array $attachments Array of attachment paths
     * @param array $options Opsi tambahan (from, cc, bcc, dll)
     * @return array Hasil pengiriman email
     */
    public function sendEmailWithAttachment($to, $subject, $body, $attachments = [], $options = [])
    {
        $data = [
            'from' => $options['from'] ?? [
                'address' => 'BAGI@ag.co.id',
                'name' => 'BAG LPJ System'
            ],
            'to' => [
                [
                    'address' => $to,
                    'name' => $options['to_name'] ?? ''
                ]
            ],
            'subject' => $subject,
            'html' => $body,
            'attachments' => []
        ];

        // Konversi attachments ke format yang benar
        foreach ($attachments as $attachment) {
            if (is_string($attachment)) {
                // Jika hanya path string
                $data['attachments'][] = [
                    'path' => $attachment,
                    'name' => basename($attachment)
                ];
            } elseif (is_array($attachment)) {
                // Jika sudah dalam format array
                $data['attachments'][] = $attachment;
            }
        }

        // Tambahkan CC jika ada
        if (isset($options['cc'])) {
            $data['cc'] = $options['cc'];
        }

        // Tambahkan BCC jika ada
        if (isset($options['bcc'])) {
            $data['bcc'] = $options['bcc'];
        }

        return $this->sendEmail($data);
    }
}
