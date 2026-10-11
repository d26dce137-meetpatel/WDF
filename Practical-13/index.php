<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 13 - Events JSON API</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="wrap">
        <h1>Event Management API</h1>
    </div>
</header>

<main class="wrap main">
    <section class="panel">
        <div class="section-heading">
            <div>
                <h2 id="formTitle">Add New Event</h2>
                <p>Enter event details. The request is sent asynchronously using Fetch API.</p>
            </div>
        </div>
        <div id="message" class="message" role="status" aria-live="polite" hidden></div>
        <form id="eventForm">
            <input type="hidden" id="eventId">
            <div class="form-grid">
                <label>Event Name
                    <input id="event_name" maxlength="120" required placeholder="e.g. Coding Competition">
                </label>
                <label>Category
                    <select id="event_category" required>
                        <option value="">Select category</option>
                        <option>Technical</option>
                        <option>Sports</option>
                        <option>Cultural</option>
                        <option>Workshop</option>
                        <option>Other</option>
                    </select>
                </label>
                <label>Event Date
                    <input type="date" id="event_date" required>
                </label>
                <label>Venue
                    <input id="venue" maxlength="150" required placeholder="e.g. Seminar Hall">
                </label>
                <label>Status
                    <select id="status" required>
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </label>
            </div>
            <div class="actions">
                <button type="submit" id="saveButton">Add Event</button>
                <button type="button" class="secondary" id="cancelButton" hidden>Cancel Edit</button>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="section-heading list-heading">
            <div>
                <h2>Events List</h2>
                <p id="countText">Loading events...</p>
            </div>
            <button type="button" class="secondary" id="refreshButton">Refresh List</button>
        </div>
        <div class="search-row">
            <label class="search-label">Search events
                <input id="searchInput" placeholder="Search name, category, venue...">
            </label>
            <label class="filter-label">Status
                <select id="statusFilter">
                    <option value="">All statuses</option>
                    <option value="Open">Open</option>
                    <option value="Closed">Closed</option>
                </select>
            </label>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Event</th><th>Category</th><th>Date</th><th>Venue</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody id="eventsBody">
                    <tr><td colspan="7" class="empty">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </section>
    <p class="footnote">Data is loaded and modified through <code>api/events.php</code>. No full page reload is required.</p>
</main>
<script>
const API_URL = 'api/events.php';
const form = document.getElementById('eventForm');
const eventsBody = document.getElementById('eventsBody');
const messageBox = document.getElementById('message');
let eventsCache = [];

function showMessage(text, type = 'success') {
    messageBox.textContent = text;
    messageBox.className = `message ${type}`;
    messageBox.hidden = false;
}
function hideMessage() {
    messageBox.hidden = true;
    messageBox.textContent = '';
}
async function apiRequest(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            'Accept': 'application/json',
            ...(options.body ? {'Content-Type': 'application/json'} : {}),
            ...(options.headers || {})
        }
    });
    let result;
    try { result = await response.json(); }
    catch { throw new Error('Server did not return valid JSON. Check PHP errors.'); }
    if (!response.ok || result.success === false) {
        throw new Error(result.message || `Request failed (${response.status}).`);
    }
    return result;
}
function safeCell(value) {
    const td = document.createElement('td');
    td.textContent = value ?? '';
    return td;
}
function renderEvents() {
    const q = document.getElementById('searchInput').value.trim().toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const filtered = eventsCache.filter(ev => {
        const searchable = `${ev.event_name} ${ev.event_category} ${ev.venue}`.toLowerCase();
        return searchable.includes(q) && (!status || ev.status === status);
    });
    document.getElementById('countText').textContent = `${filtered.length} event(s) shown`;
    eventsBody.replaceChildren();
    if (!filtered.length) {
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = 7;
        td.className = 'empty';
        td.textContent = 'No events found. Add a new event or change the search.';
        tr.appendChild(td);
        eventsBody.appendChild(tr);
        return;
    }
    filtered.forEach(ev => {
        const tr = document.createElement('tr');
        tr.appendChild(safeCell(ev.event_id));
        tr.appendChild(safeCell(ev.event_name));
        tr.appendChild(safeCell(ev.event_category));
        tr.appendChild(safeCell(ev.event_date));
        tr.appendChild(safeCell(ev.venue));
        const statusTd = safeCell(ev.status);
        statusTd.className = ev.status === 'Open' ? 'status-open' : 'status-closed';
        tr.appendChild(statusTd);
        const actions = document.createElement('td');
        actions.className = 'row-actions';
        const edit = document.createElement('button');
        edit.type = 'button'; edit.className = 'small secondary'; edit.textContent = 'Edit';
        edit.addEventListener('click', () => startEdit(ev.event_id));
        const del = document.createElement('button');
        del.type = 'button'; del.className = 'small danger'; del.textContent = 'Delete';
        del.addEventListener('click', () => deleteEvent(ev.event_id, ev.event_name));
        actions.append(edit, del);
        tr.appendChild(actions);
        eventsBody.appendChild(tr);
    });
}
async function loadEvents() {
    eventsBody.innerHTML = '<tr><td colspan="7" class="empty">Loading events...</td></tr>';
    try {
        const result = await apiRequest(API_URL);
        eventsCache = result.data || [];
        renderEvents();
    } catch (error) {
        eventsBody.innerHTML = '<tr><td colspan="7" class="empty">Could not load events.</td></tr>';
        showMessage(error.message, 'error');
    }
}
function resetForm() {
    form.reset();
    document.getElementById('eventId').value = '';
    document.getElementById('formTitle').textContent = 'Add New Event';
    document.getElementById('saveButton').textContent = 'Add Event';
    document.getElementById('cancelButton').hidden = true;
}
async function startEdit(id) {
    hideMessage();
    try {
        const result = await apiRequest(`${API_URL}?id=${encodeURIComponent(id)}`);
        const ev = result.data;
        document.getElementById('eventId').value = ev.event_id;
        document.getElementById('event_name').value = ev.event_name;
        document.getElementById('event_category').value = ev.event_category;
        document.getElementById('event_date').value = ev.event_date;
        document.getElementById('venue').value = ev.venue;
        document.getElementById('status').value = ev.status;
        document.getElementById('formTitle').textContent = `Edit Event #${ev.event_id}`;
        document.getElementById('saveButton').textContent = 'Update Event';
        document.getElementById('cancelButton').hidden = false;
        form.scrollIntoView({behavior: 'smooth', block: 'start'});
    } catch (error) { showMessage(error.message, 'error'); }
}
form.addEventListener('submit', async (event) => {
    event.preventDefault();
    hideMessage();
    const id = document.getElementById('eventId').value;
    const payload = {
        event_name: document.getElementById('event_name').value.trim(),
        event_category: document.getElementById('event_category').value,
        event_date: document.getElementById('event_date').value,
        venue: document.getElementById('venue').value.trim(),
        status: document.getElementById('status').value
    };
    try {
        const result = await apiRequest(id ? `${API_URL}?id=${encodeURIComponent(id)}` : API_URL, {
            method: id ? 'PUT' : 'POST',
            body: JSON.stringify(payload)
        });
        showMessage(result.message || 'Saved successfully.');
        resetForm();
        await loadEvents();
    } catch (error) { showMessage(error.message, 'error'); }
});
async function deleteEvent(id, name) {
    if (!confirm(`Delete "${name}"? This action cannot be undone.`)) return;
    hideMessage();
    try {
        const result = await apiRequest(`${API_URL}?id=${encodeURIComponent(id)}`, {method: 'DELETE'});
        showMessage(result.message || 'Event deleted.');
        if (document.getElementById('eventId').value === String(id)) resetForm();
        await loadEvents();
    } catch (error) { showMessage(error.message, 'error'); }
}
document.getElementById('cancelButton').addEventListener('click', resetForm);
document.getElementById('refreshButton').addEventListener('click', loadEvents);
document.getElementById('searchInput').addEventListener('input', renderEvents);
document.getElementById('statusFilter').addEventListener('change', renderEvents);
loadEvents();
</script>
</body>
</html>
