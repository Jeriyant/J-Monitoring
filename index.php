<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Device Monitor</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 20px;
        }
        table {
            margin-top: 20px;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th {
            font-size: 14px;
            text-align: center; /* Meratakan teks judul kolom ke tengah */
        }
        td {
            font-size: 12px;
            text-align: left; /* Meratakan teks konten kolom ke kiri */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ?>
    <div class="container">
        <h1>J-MONITORING</h1>
        <h2>Network Device Monitor</h2>

        <button id="show-form-btn" class="btn btn-success mb-3">Tambah Perangkat</button>

        <div id="form-container" class="form-container" style="display: none;">
            <form id="device-form" method="POST" action="manage_devices.php">
                <input type="hidden" name="action" id="form-action" value="add">
                <input type="hidden" name="device_id" id="device-id">
                <div class="form-group">
                    <label for="device-name">Nama Perangkat:</label>
                    <input type="text" class="form-control" id="device-name" name="device_name" required>
                </div>
                <div class="form-group">
                    <label for="device-ip">IP Address:</label>
                    <input type="text" class="form-control" id="device-ip" name="device_ip" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm()">Batal</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="thead-dark">
                    <tr>
                        <th>Nama</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Latency (ms)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $devices = [];
                    if (file_exists('devices.txt')) {
                        $devices_content = file_get_contents('devices.txt');
                        echo "<!-- Isi file devices.txt: $devices_content -->";
                        $devices = json_decode($devices_content, true);
                        if (!is_array($devices)) {
                            echo "<tr><td colspan='5' class='text-center'>Gagal mendekode JSON dalam file devices.txt</td></tr>";
                            $devices = [];
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>File devices.txt tidak ditemukan</td></tr>";
                    }

                    foreach ($devices as $id => $device) {
                        echo "<tr>";
                        echo "<td id='{$device['name']}-name'>{$device['name']}</td>";
                        echo "<td id='{$device['name']}-ip'>{$device['ip']}</td>";
                        echo "<td id='{$device['name']}-status' class='text-center'>Loading</td>";
                        echo "<td id='{$device['name']}-latency' class='text-center'>Loading</td>";
                        echo "<td class='text-center'>
                                <button class='btn btn-warning btn-sm' onclick=\"editDevice('$id', '{$device['name']}', '{$device['ip']}')\">Edit</button>
                                <button class='btn btn-danger btn-sm' onclick=\"deleteDevice('$id')\">Hapus</button>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <p class="text-center">Directed by JNET™ Telecom Networking Solution ©2024</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function updateStatusAndLatency() {
            <?php foreach ($devices as $name => $device): ?>
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var response = JSON.parse(this.responseText);
                        console.log(response); // Debugging line
                        document.getElementById('<?php echo $device['name']; ?>-status').innerHTML = response.status;
                        document.getElementById('<?php echo $device['name']; ?>-latency').innerHTML = response.latency;
                    } else if (this.readyState == 4) {
                        document.getElementById('<?php echo $device['name']; ?>-status').innerHTML = "<span style='color: red;'>Offline</span>";
                        document.getElementById('<?php echo $device['name']; ?>-latency').innerHTML = 'N/A';
                        console.error('Error fetching status for <?php echo $device['name']; ?>');
                    }
                };
                xhttp.open("GET", "update.php?ip=<?php echo $device['ip']; ?>", true);
                xhttp.send();
            <?php endforeach; ?>
        }

        setInterval(updateStatusAndLatency, 5000); // Update every 5 seconds

        function showForm() {
            document.getElementById('form-container').style.display = 'block';
            document.getElementById('show-form-btn').style.display = 'none';
        }

        function hideForm() {
            document.getElementById('form-container').style.display = 'none';
            document.getElementById('show-form-btn').style.display = 'block';
            document.getElementById('device-form').reset();
            document.getElementById('form-action').value = 'add';
        }

        document.getElementById('show-form-btn').addEventListener('click', showForm);

        function editDevice(id, name, ip) {
            document.getElementById('form-action').value = 'edit';
            document.getElementById('device-id').value = id;
            document.getElementById('device-name').value = name;
            document.getElementById('device-ip').value = ip;
            showForm();
        }

        function deleteDevice(id) {
            if (confirm('Apakah Anda yakin ingin menghapus perangkat ini?')) {
                var form = document.getElementById('device-form');
                document.getElementById('form-action').value = 'delete';
                document.getElementById('device-id').value = id;
                form.submit();
            }
        }
    </script>
</body>
</html>
