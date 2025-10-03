<style>
  .sidebar .btn {
  background: none;
  border: none;
  font-size: 15px;
  padding: 0;
}
.sidebar .btn:focus {
  box-shadow: none;
}
.sidebar .list-group-item {
  border: none;
  padding: 6px 0;
}
.sidebar h6 {
  font-size: 14px;
  margin-top: 10px;
}
</style>

<div class="sidebar p-3 bg-white shadow-sm rounded">
    <!-- Messages Section -->
    <div class="mb-4">
        <button class="btn w-100 d-flex justify-content-between align-items-center text-start fw-bold" 
                data-bs-toggle="collapse" data-bs-target="#messagesCollapse">
            Messages
            <i class="bi bi-chevron-down"></i>
        </button>
        <div class="collapse show" id="messagesCollapse">
            <!-- Tabs -->
            <ul class="nav nav-tabs mt-2" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#inbox">Inbox <span class="badge bg-secondary">0</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#sent">Sent <span class="badge bg-primary">3</span></a>
                </li>
            </ul>

            <!-- Tab Contents -->
            <div class="tab-content mt-2">
                <div class="tab-pane fade show active" id="inbox">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between">Pending <span class="badge bg-secondary">0</span></li>
                        <li class="list-group-item d-flex justify-content-between">Accepted <span class="badge bg-secondary">0</span></li>
                        <li class="list-group-item d-flex justify-content-between">Replied <span class="badge bg-secondary">0</span></li>
                        <li class="list-group-item d-flex justify-content-between">Need time/info <span class="badge bg-secondary">0</span></li>
                        <li class="list-group-item d-flex justify-content-between">Declined <span class="badge bg-secondary">0</span></li>
                    </ul>
                </div>
                <div class="tab-pane fade" id="sent">
                    <p class="text-muted small">No sent items yet.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Section -->
    <div class="mb-4">
        <button class="btn w-100 d-flex justify-content-between align-items-center text-start fw-bold" 
                data-bs-toggle="collapse" data-bs-target="#requestCollapse">
            Request
            <i class="bi bi-chevron-down"></i>
        </button>
        <div class="collapse" id="requestCollapse">
            <p class="text-muted small mt-2">No requests available.</p>
        </div>
    </div>

    <!-- Chat History -->
    <div class="mb-4">
        <h6 class="fw-bold border-bottom pb-2">Chat History</h6>
        <p class="text-muted small">No chats yet.</p>
    </div>

    <!-- Activity Board -->
    <div>
        <h6 class="fw-bold border-bottom pb-2">Your Activity Board</h6>
        <ul class="list-group list-group-flush small">
            <li class="list-group-item d-flex justify-content-between">SMS Sent <span class="badge bg-secondary">0</span></li>
            <li class="list-group-item d-flex justify-content-between">SMS Received <span class="badge bg-secondary">0</span></li>
        </ul>
    </div>
</div>
