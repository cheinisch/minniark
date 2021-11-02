            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
          <span>Essays</span>
          <a class="link-secondary" href="#" aria-label="Add a new report">
            <span data-feather="plus-circle"></span>
          </a>
        </h6>
        <ul class="nav flex-column mb-2">
          <li class="nav-item">
            <a class="nav-link" href="#">
              <span data-feather="file-text"></span>
              Essay 1
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">
              <span data-feather="file-text"></span>
              Essay 2
            </a>
          </li>
        </ul>
        </div>
    </nav>
    <main class="col-md-8 ms-sm-auto col-lg-8 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Essays</h1>
      </div>
      <form method="post" th:object="${post}">

                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" placeholder="Title" autocomplete="off" class="form-control"
                           th:field="*{title}"/>
                </div>

                <div id="editor">
                  <p>Hello World!</p>
                  <p>Some initial <strong>bold</strong> text</p>
                  <p><br></p>
                </div>

                <button class="btn btn-primary" type="submit">Submit form</button>
            </form>
    </main>
    <div id="sidebar" class="col-lg-2 bg-light">
      
          </div>
