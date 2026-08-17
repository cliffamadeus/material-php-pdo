### 1. Open PowerShell

From your current:

```text
E:\xampp\mysql\bin>
```

type:

```cmd
powershell
```

You should get:

```text
PS E:\xampp\mysql\bin>
```

### 2. Use this command

This version behaves more like `ping -t`: it **doesn't clear the screen**. Every new check is printed underneath the previous one.

```powershell
while ($true) {
    $log = .\mysql.exe -u root material_pdo -N -e "SELECT id,email,action,status,ip_address,created_at FROM activity_logs ORDER BY id DESC LIMIT 1;"

    if ($log) {
        Write-Host "$(Get-Date -Format 'HH:mm:ss')  $log"
    }

    Start-Sleep -Seconds 1
}
```

If your MySQL root account has a password, use:

```powershell
while ($true) {
    $log = .\mysql.exe -u root -p material_pdo -N -e "SELECT id,email,action,status,ip_address,created_at FROM activity_logs ORDER BY id DESC LIMIT 1;"

    if ($log) {
        Write-Host "$(Get-Date -Format 'HH:mm:ss')  $log"
    }

    Start-Sleep -Seconds 1
}
```

However, that will print the **same latest record every second**. We can improve it so it behaves more like a real event monitor.

### 3. Recommended version — show only NEW logs

```powershell
$lastId = 0

while ($true) {

    $log = .\mysql.exe -u root material_pdo -N -e "SELECT id,email,action,status,ip_address,created_at FROM activity_logs WHERE id > $lastId ORDER BY id ASC;"

    if ($log) {

        foreach ($line in $log) {

            Write-Host "$(Get-Date -Format 'HH:mm:ss')  $line"

            $parts = $line -split "`t"

            if ($parts.Length -gt 0) {
                $lastId = [int]$parts[0]
            }
        }
    }

    Start-Sleep -Seconds 1
}
```

Now the behavior is:

```text
PS E:\xampp\mysql\bin>

13:42:01  21  user@example.com    login            success  127.0.0.1  2026-08-17 13:42:01
13:42:07  22  user@example.com    view_dashboard   success  127.0.0.1  2026-08-17 13:42:07
13:42:15  23  test@example.com    login            failed   127.0.0.1  2026-08-17 13:42:15
13:42:21  24  admin@example.com   update_record    success  127.0.0.1  2026-08-17 13:42:21
```

Then when **nothing happens**, it simply waits—just like `ping -t` waits between responses.

When you perform another action in your PHP application:

```text
Browser
   ↓
PHP
   ↓
logActivity()
   ↓
MySQL
   ↓
PowerShell
   ↓
NEW LOG APPEARS
```

### Stop it

Just press:

```text
Ctrl + C
```

### One thing I recommend for your project

For your **audit/logging demonstration**, this last version is much better than refreshing the entire table. It demonstrates the idea of a **live audit trail**:

```text
=============================================================
              LIVE DATABASE ACTIVITY MONITOR
=============================================================

13:42:01  21  user@example.com   login          success
13:42:07  22  user@example.com   view_dashboard success
13:42:15  23  test@example.com   login          failed
13:42:21  24  admin@example.com  update_record  success

Waiting for new activity...
```

