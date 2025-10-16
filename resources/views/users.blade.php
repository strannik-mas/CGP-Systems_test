<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Users list</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Bundle JS (с Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{--<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>--}}
</head>
<body>
<div class="container my-4">

    <h2 class="text-center mb-4">Users sorted by number of images</h2>

    <!-- Таблица пользователей -->
    <table class="table table-striped table-hover" id="users-table">
        <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>City</th>
            <th>Images Count</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Bootstrap пагинация -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center" id="pagination"></ul>
    </nav>

    <!-- Кнопка открытия модального окна -->
    <div class="text-center my-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
            Создать пользователя
        </button>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="user-form" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Создать пользователя</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Имя</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">Город</label>
                    <input type="text" class="form-control" id="city" name="city" required>
                </div>
                <div id="images-wrapper" class="mb-3"></div>
                <!-- Контейнер для превью -->
                <div id="images-preview" class="mb-3 d-flex flex-wrap"></div>
                <a href="#" id="add-image">Добавить еще один файл</a>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Создать</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            </div>
        </form>
    </div>
</div>


<script>
    /*let currentPage = 1;

    function loadUsers(page = 1) {
        $.ajax({
            url: `/api/users?page=${page}`,
            type: 'GET',
            success: function (response) {
                const tbody = $('#users-table tbody');
                tbody.empty();

                response.data.forEach(user => {
                    tbody.append(`
                            <tr>
                                <td>${user.name}</td>
                                <td>${user.city}</td>
                                <td>${user.images_count}</td>
                            </tr>
                        `);
                });

                // пагинация
                const pagination = $('.pagination');
                pagination.empty();

                if (response.prev_page_url) {
                    pagination.append(`<button onclick="loadUsers(${page - 1})">← Prev</button>`);
                }

                pagination.append(`<span> Page ${response.current_page} of ${response.last_page} </span>`);

                if (response.next_page_url) {
                    pagination.append(`<button onclick="loadUsers(${page + 1})">Next →</button>`);
                }
            },
            error: function () {
                alert('Error loading users.');
            }
        });
    }

    $(() => {
        loadUsers();

        // добавить новое поле для картинки
        $('#add-image').click(function(e){
            e.preventDefault();
            $('#images-wrapper').append('<input type="file" class="form-control mb-2" name="images[]" accept="image/!*">');
        });
    });*/

    let currentPage = 1;
    let inputCounter = 0;

    //Функции работы с файлами

    // Создание нового input и инициализация превью
    function createFileInput(wrapper) {
        const input = document.createElement('input');
        input.type = 'file';
        input.name = 'images[]';
        input.accept = 'image/*';
        input.className = 'form-control mb-2';
        input.dataset.id = inputCounter++;

        wrapper.appendChild(input);
        initPreview(input); // навешиваем превью на этот input
    }

    // Инициализация превью для одного input
    function initPreview(input) {
        input.addEventListener('change', function() {
            const previewContainer = document.getElementById('images-preview');

            // Удаляем старое превью только для этого input
            const existing = previewContainer.querySelectorAll(`img[data-input-id="${input.dataset.id}"]`);
            existing.forEach(img => img.remove());

            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    img.style.marginRight = '10px';
                    img.style.marginBottom = '10px';
                    img.dataset.inputId = input.dataset.id;
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // Загрузка пользователей
    function loadUsers(page = 1) {
        fetch(`/api/users?page=${page}`)
            .then(res => res.json())
            .then(response => {
                const tbody = document.querySelector('#users-table tbody');
                tbody.innerHTML = '';

                response.data.forEach(user => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                    <td>${user.name}</td>
                    <td>${user.city}</td>
                    <td>${user.images_count}</td>
                `;
                    tbody.appendChild(tr);
                });

                // Пагинация
                const pagination = document.getElementById('pagination');
                pagination.innerHTML = '';

                const prevDisabled = response.prev_page_url ? '' : 'disabled';
                const nextDisabled = response.next_page_url ? '' : 'disabled';

                // Prev
                const prevItem = document.createElement('li');
                prevItem.className = `page-item ${prevDisabled}`;
                const prevLink = document.createElement('a');
                prevLink.className = 'page-link';
                prevLink.href = '#';
                prevLink.textContent = 'Previous';
                prevLink.onclick = function(e) {
                    e.preventDefault();
                    if (response.prev_page_url) loadUsers(currentPage - 1);
                };
                prevItem.appendChild(prevLink);
                pagination.appendChild(prevItem);

                // Текущая страница
                const currentItem = document.createElement('li');
                currentItem.className = 'page-item active';
                currentItem.innerHTML = `<span class="page-link">${response.current_page}</span>`;
                pagination.appendChild(currentItem);

                // Next
                const nextItem = document.createElement('li');
                nextItem.className = `page-item ${nextDisabled}`;
                const nextLink = document.createElement('a');
                nextLink.className = 'page-link';
                nextLink.href = '#';
                nextLink.textContent = 'Next';
                nextLink.onclick = function(e) {
                    e.preventDefault();
                    if (response.next_page_url) loadUsers(currentPage + 1);
                };
                nextItem.appendChild(nextLink);
                pagination.appendChild(nextItem);

                currentPage = response.current_page;
            })
            .catch(err => alert('Ошибка загрузки данных'));
    }

    //Инициализация первого поля
    const wrapper = document.getElementById('images-wrapper');
    createFileInput(wrapper);

    // Кнопка "Добавить еще один файл"
    document.getElementById('add-image').addEventListener('click', function(e){
        e.preventDefault();
        createFileInput(wrapper);
    });

    // Отправка формы через AJAX
    document.getElementById('user-form').addEventListener('submit', function(e){
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/users/store', {
            method: 'POST',
            body: formData
        })
            .then(res => {
                if (!res.ok) throw new Error('Ошибка при создании пользователя');
                return res.json();
            })
            .then(response => {
                if (response.status === 'success') {
                    alert('Пользователь создан!');

                    // Сброс формы и превью
                    this.reset();
                    document.getElementById('images-preview').innerHTML = '';
                    wrapper.innerHTML = '';
                    createFileInput(wrapper);

                    // Скрываем модальное окно
                    const modalEl = document.getElementById('userModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();

                    // Обновление таблицы
                    loadUsers(currentPage);
                } else {
                    alert('Ошибка при создании пользователя');
                }
            })
            .catch(err => alert(err.message));
    });

    //Первая загрузка таблицы
    document.addEventListener('DOMContentLoaded', loadUsers(currentPage));
</script>
</body>
</html>
