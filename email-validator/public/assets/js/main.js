const textarea = $("#emails");
const error = $("#client-error");
const results = $("#results");
const resultsBody = $("#results-table tbody");
const loading = $("#loading-indicator");

const debounceDelay = 900;
let debounceTimer = null;

function normalizeInput(text) {
    return text
        .split(/[\n,]+/)
        .map((s) => s.trim())
        .filter(Boolean);
}

function showClientError(msg) {
    error.text(msg).show();
}

function clearClientError() {
    error.hide().text("");
}

function showResults(res) {
    resultsBody.empty();

    res.forEach((r, index) => {
        const isValid = r.status === "valid";

        const statusText = isValid ? "Действующая" : "Ошибка";
        const badgeClass = isValid ? "bg-success" : "bg-danger";

        const message = r.message ? r.message : "—";

        resultsBody.append(`
  <tr>
    <td>${index + 1}</td>
    <td>${escapeHtml(r.email)}</td>
    <td><span class="badge ${badgeClass}">${statusText}</span></td>
    <td>${escapeHtml(message)}</td>
  </tr>
`);
    });
    results.show();
}

function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, function (m) {
        return {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;",
        }[m];
    });
}

function sendValidation(emails) {
    if (!emails.length) {
        clearClientError();
        results.hide();
        return;
    }

    if (emails.length > 10) {
        showClientError(
            "Превышен лимит: можно отправлять максимум 10 email за запрос.",
        );
        results.hide();
        return;
    }

    clearClientError();
    loading.show();

    setTimeout(() => {
        $.ajax({
            url: "/index.php",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ emails }),
            dataType: "json",
        })
            .done(function (resp) {
                if (resp && resp.success) {
                    showResults(resp.results || []);
                } else if (resp && resp.error) {
                    showClientError(resp.error);
                    results.hide();
                } else {
                    showClientError("Неожиданная ошибка сервера.");
                    results.hide();
                }
            })
            .fail(function (jqxhr) {
                console.log(jqxhr);
                if (jqxhr.responseJSON && jqxhr.responseJSON.error) {
                    showClientError(jqxhr.responseJSON.error);
                } else {
                    showClientError("Ошибка сети или сервера.");
                }
                results.hide();
            })
            .always(function () {
                loading.hide();
            });
    }, 5000);
}

textarea.on("input", function () {
    clearClientError();
    results.hide();

    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
        const text = textarea.val();
        const emails = normalizeInput(text);
        if (emails.length > 10) {
            showClientError(
                "Превышен лимит: можно отправлять максимум 10 email за запрос.",
            );
            return;
        }
        sendValidation(emails);
    }, debounceDelay);
});

$("#check-btn").on("click", function () {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
        debounceTimer = null;
    }
    const emails = normalizeInput(textarea.val());
    sendValidation(emails);
});

textarea.on("keydown", function () {
    clearClientError();
});