document.addEventListener("DOMContentLoaded", function () {
    const apiURL = "http://localhost/siswa_api/backend/api.php"; // Sesuaikan dengan API kamu
    const siswaTable = document.getElementById("siswaTable");

    function fetchData() {
        fetch(apiURL)
            .then(response => response.json())
            .then(data => {
                siswaTable.innerHTML = "";
                data.forEach((siswa, index) => {
                    siswaTable.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${siswa.nama}</td>
                            <td>${siswa.jk}</td>
                            <td>${siswa.alamat}</td>
                            <td><img src="http://localhost/siswa_api/images/${siswa.foto}" width="50"></td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-btn" data-id="${siswa.id}">Edit</button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${siswa.id}">Hapus</button>
                            </td>
                        </tr>
                    `;
                });

                document.querySelectorAll(".edit-btn").forEach(button => {
                    button.addEventListener("click", function () {
                        let id = this.dataset.id;
                        editData(id);
                    });
                });

                document.querySelectorAll(".delete-btn").forEach(button => {
                    button.addEventListener("click", function () {
                        let id = this.dataset.id;
                        deleteData(id);
                    });
                });
            })
            .catch(error => console.error("Error fetching data:", error));
    }

    document.getElementById("siswaForm").addEventListener("submit", function (event) {
        event.preventDefault();
        
        let formData = new FormData(this);
        let siswaId = document.getElementById("siswaId").value;

        if (siswaId) {
            formData.append("action", "tambah");
            formData.append("id", siswaId);
        } else {
            formData.append("action", "tambah");
        }

        fetch(apiURL + "?action=tambah", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            fetchData();
            document.getElementById("siswaForm").reset();
            var myModal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
            myModal.hide();
        })
        .catch(error => console.error("Error:", error));
    });

    function editData(id) {
        fetch(`${apiURL}?id=${id}`)
            .then(response => response.json())
            .then(siswa => {
                document.getElementById("siswaId").value = siswa.id;
                document.getElementById("nama").value = siswa.nama;
                document.getElementById("jk").value = siswa.jk;
                document.getElementById("alamat").value = siswa.alamat;
                var myModal = new bootstrap.Modal(document.getElementById('addModal'));
                myModal.show();
            })
            .catch(error => console.error("Error fetching siswa:", error));
    }

    function deleteData(id) {
        if (confirm("Apakah kamu yakin ingin menghapus data ini?")) {
            fetch(`${apiURL}?id=${id}&action=hapus`, { method: "GET" })
            .then(response => response.json())
            .then(result => {
                alert(result.message);
                fetchData();
            })
            .catch(error => console.error("Error deleting data:", error));
        }
    }

    fetchData();
});
