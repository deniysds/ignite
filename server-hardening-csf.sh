#!/bin/bash
# ==============================================================================
# Skrip Otomasi Pengamanan Server & Instalasi Firewall CSF/LFD
# Target: CentOS / AlmaLinux 8 / 9 / Rocky Linux / RHEL dengan WHM/cPanel
# ==============================================================================

set -e

echo "=== [1/6] Memeriksa Hak Akses Root ==="
if [ "$EUID" -ne 0 ]; then
  echo "Error: Skrip ini harus dijalankan dengan hak akses root (sudo)."
  exit 1
fi

echo "=== [2/6] Memperbarui Repositori & Menginstal Paket Dependensi ==="
if command -v dnf &> /dev/null; then
    dnf install -y perl perl-libwww-perl perl-LWP-Protocol-https perl-GDGraph iptables ipset bind-utils tar wget curl firewalld-filesystem
elif command -v yum &> /dev/null; then
    yum install -y perl perl-libwww-perl perl-LWP-Protocol-https perl-GDGraph iptables ipset bind-utils tar wget curl
fi

# Matikan firewalld jika aktif agar tidak bentrok dengan CSF/iptables
if systemctl is-active --quiet firewalld; then
    echo "Menonaktifkan firewalld bawaan sistem..."
    systemctl stop firewalld
    systemctl disable firewalld
fi

echo "=== [3/6] Mengunduh & Menginstal ConfigServer Security & Firewall (CSF) ==="
cd /usr/src
rm -rf csf csf.tgz
wget -q https://download.configserver.com/csf.tgz
tar -xzf csf.tgz
cd csf

if [ -f "install.cpanel.sh" ] && [ -d "/usr/local/cpanel" ]; then
    echo "Terdeteksi cPanel/WHM. Menjalankan install.cpanel.sh..."
    sh install.cpanel.sh
else
    echo "Menjalankan instalasi umum CSF..."
    sh install.sh
fi

echo "=== [4/6] Menguji Kesiapan Modul Kernel Iptables ==="
perl /usr/local/csf/bin/csftest.pl

echo "=== [5/6] Menerapkan Hardening Konfigurasi /etc/csf/csf.conf ==="
CSF_CONF="/etc/csf/csf.conf"

if [ -f "$CSF_CONF" ]; then
    # Backup file konfigurasi asli
    cp "$CSF_CONF" "${CSF_CONF}.backup.$(date +%F_%T)"

    # Matikan mode testing (aktifkan proteksi aktif)
    sed -i 's/^TESTING = .*/TESTING = "0"/' "$CSF_CONF"

    # Batasi port TCP Inbound (Aman: HTTP, HTTPS, SSH, DNS, cPanel SSL 2083, WHM SSL 2087, Webmail SSL 2096)
    sed -i 's/^TCP_IN = .*/TCP_IN = "20,21,22,25,53,80,110,143,443,465,587,993,995,2077,2078,2083,2086,2087,2096"/' "$CSF_CONF"
    
    # Batasi port TCP Outbound
    sed -i 's/^TCP_OUT = .*/TCP_OUT = "20,21,22,25,53,80,110,113,443,587,993,995,2083,2087"/' "$CSF_CONF"

    # UDP In/Out
    sed -i 's/^UDP_IN = .*/UDP_IN = "20,21,53"/' "$CSF_CONF"
    sed -i 's/^UDP_OUT = .*/UDP_OUT = "20,21,53,113,123"/' "$CSF_CONF"

    # Aktifkan mitigasi SYN Flood & Connection Tracking Limit
    sed -i 's/^SYNFLOOD = .*/SYNFLOOD = "1"/' "$CSF_CONF"
    sed -i 's/^SYNFLOOD_RATE = .*/SYNFLOOD_RATE = "100\/s"/' "$CSF_CONF"
    sed -i 's/^SYNFLOOD_BURST = .*/SYNFLOOD_BURST = "150"/' "$CSF_CONF"

    # Konfigurasi LFD Brute-Force Triggers (Ketahanan Maksimal)
    sed -i 's/^LF_SSHD = .*/LF_SSHD = "5"/' "$CSF_CONF"
    sed -i 's/^LF_CPANEL = .*/LF_CPANEL = "5"/' "$CSF_CONF"
    sed -i 's/^LF_FTPD = .*/LF_FTPD = "5"/' "$CSF_CONF"
    sed -i 's/^LF_POP3D = .*/LF_POP3D = "5"/' "$CSF_CONF"
    sed -i 's/^LF_IMAPD = .*/LF_IMAPD = "5"/' "$CSF_CONF"
    sed -i 's/^LF_TRIGGER_PERM = .*/LF_TRIGGER_PERM = "3600"/' "$CSF_CONF"

    # Aktifkan blokir port scan
    sed -i 's/^PS_INTERVAL = .*/PS_INTERVAL = "120"/' "$CSF_CONF"
    sed -i 's/^PS_LIMIT = .*/PS_LIMIT = "10"/' "$CSF_CONF"

    echo "Konfigurasi csf.conf berhasil diperbarui."
fi

echo "=== [6/6] Menjalankan dan Memulai Layanan CSF & LFD ==="
csf -e
csf -r

systemctl enable csf
systemctl enable lfd
systemctl restart csf
systemctl restart lfd

echo "=============================================================================="
echo "SUKSES: Pengamanan Server dan Firewall CSF/LFD telah aktif secara penuh!"
echo "- Status CSF: $(systemctl is-active csf)"
echo "- Status LFD: $(systemctl is-active lfd)"
echo "- Antarmuka Web WHM: Menu 'ConfigServer Security & Firewall' kini tersedia di WHM."
echo "=============================================================================="
