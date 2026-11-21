const warnetStore = {
  zoneFilter: "ALL",
  onlyAvailable: false,
  selectedZoneId: 1,
  selectedPackageId: 2,
  customHours: 3,
  useCustomHours: false,
  zones: [
    { id: 1, name: "Commoner", ratePerHour: 7000, cpu: "Core i5-12400F", gpu: "RTX 3060", ram: "16GB DDR4", monitor: "144Hz FHD" },
    { id: 2, name: "Noble", ratePerHour: 12000, cpu: "Core i7-13700F", gpu: "RTX 4070", ram: "32GB DDR5", monitor: "240Hz FHD" },
    { id: 3, name: "Royalty", ratePerHour: 25000, cpu: "Core i9-14900K", gpu: "RTX 4080 Super", ram: "64GB DDR5", monitor: "240Hz QHD" }
  ],
  packages: [
    { id: 1, zoneId: 1, name: "Paket Santai", hours: 2, price: 14000, active: true },
    { id: 2, zoneId: 1, name: "Paket Mabar", hours: 5, price: 30000, active: true },
    { id: 3, zoneId: 2, name: "Noble Pass", hours: 3, price: 34000, active: true },
    { id: 4, zoneId: 3, name: "Royalty Session", hours: 2, price: 48000, active: true }
  ],
  pcs: [
    { id: "PC-01", zoneId: 1, ip: "192.168.1.101", status: "Tersedia", user: null },
    { id: "PC-02", zoneId: 1, ip: "192.168.1.102", status: "Digunakan", user: "capuri" },
    { id: "PC-03", zoneId: 1, ip: "192.168.1.103", status: "Tersedia", user: null },
    { id: "PC-04", zoneId: 2, ip: "192.168.1.104", status: "Digunakan", user: "viper987" },
    { id: "PC-05", zoneId: 2, ip: "192.168.1.105", status: "Tersedia", user: null },
    { id: "PC-06", zoneId: 3, ip: "192.168.1.106", status: "Maintenance", user: null }
  ],
  accounts: [
    { id: "NX-882049", username: "capuri", name: "Oguri Cap", phone: "0812-9988-1122", status: "Aktif", quota: { 1: 275, 2: 130, 3: 0 }, activePc: "PC-02" },
    { id: "NX-801011", username: "viper987", name: "Viper 987", phone: "0812-3456-7890", status: "Aktif", quota: { 1: 180, 2: 45, 3: 60 }, activePc: "PC-04" },
    { id: "NX-831111", username: "brim23124", name: "Brimstone", phone: "0832-0009-2312", status: "Aktif", quota: { 1: 90, 2: 0, 3: 0 }, activePc: null }
  ],
  transactions: [
    { id: "TX-993821", member: "capuri", time: "Hari ini, 18:24 WIB", label: "Commoner - Paket Mabar (5 Jam)", method: "QRIS Dinamis", amount: 30000 },
    { id: "TX-993411", member: "viper987", time: "Kemarin, 20:10 WIB", label: "Noble - Noble Pass (3 Jam)", method: "Virtual Account", amount: 34000 }
  ]
};

function formatRupiah(num) {
  return "Rp " + Number(num || 0).toLocaleString("id-ID");
}

function formatMinutes(mins) {
  const safe = Math.max(0, Number(mins || 0));
  const h = Math.floor(safe / 60);
  const m = safe % 60;
  return `${h}j ${String(m).padStart(2, "0")}m`;
}

function getZoneById(id) {
  return warnetStore.zones.find(z => z.id === Number(id)) || warnetStore.zones[0];
}

function getBackendProfilePayload() {
  const rootEl = document.getElementById("profile-page-root");
  let userData = window.warnetUserData || null;
  let billingData = window.warnetBillingData || null;

  if (rootEl) {
    if (rootEl.dataset.user) {
      try {
        userData = JSON.parse(rootEl.dataset.user);
      } catch (_) {}
    }
    if (rootEl.dataset.billing) {
      try {
        billingData = JSON.parse(rootEl.dataset.billing);
      } catch (_) {}
    }
  }

  return { userData, billingData };
}

function getCurrentSession() {
  const { userData: backendUser } = getBackendProfilePayload();
  return {
    username: sessionStorage.getItem("warnet_user") || backendUser?.username || "capuri",
    role: sessionStorage.getItem("warnet_role") || "user"
  };
}

function getActiveMember() {
  const sess = getCurrentSession();
  return warnetStore.accounts.find(a => a.username === sess.username) || warnetStore.accounts[0];
}

function extractQuotaFromBilling(billingData, fallbackQuota) {
  const defaultQuota = {
    1: fallbackQuota?.[1] ?? 0,
    2: fallbackQuota?.[2] ?? 0,
    3: fallbackQuota?.[3] ?? 0
  };

  if (!billingData) return defaultQuota;

  if (Array.isArray(billingData)) {
    const result = { ...defaultQuota };
    billingData.forEach(item => {
      if (!item) return;
      const zoneVal = String(item.zona ?? item.zone ?? item.nama_zona ?? item.id_zona ?? "").toLowerCase();
      const mins = Number(item.sisa_menit ?? item.menit ?? item.kuota ?? item.saldo ?? 0);
      if (zoneVal === "commoner" || zoneVal === "1") result[1] = mins;
      if (zoneVal === "noble" || zoneVal === "2") result[2] = mins;
      if (zoneVal === "royalty" || zoneVal === "3") result[3] = mins;
    });
    return result;
  }

  if (typeof billingData === "object") {
    return {
      1: Number(
        billingData.commoner ??
        billingData.sisa_commoner ??
        billingData.quota_commoner ??
        billingData[1] ??
        defaultQuota[1]
      ),
      2: Number(
        billingData.noble ??
        billingData.sisa_noble ??
        billingData.quota_noble ??
        billingData[2] ??
        defaultQuota[2]
      ),
      3: Number(
        billingData.royalty ??
        billingData.sisa_royalty ??
        billingData.quota_royalty ??
        billingData[3] ??
        defaultQuota[3]
      )
    };
  }

  return defaultQuota;
}

function getResolvedProfileMember() {
  const { userData: backendUser, billingData: backendBilling } = getBackendProfilePayload();
  const fallbackMember = getActiveMember();

  if (!backendUser && !fallbackMember) return null;

  return {
    id: backendUser?.id_akun ?? backendUser?.id ?? fallbackMember?.id ?? "-",
    username: backendUser?.username ?? fallbackMember?.username ?? "member",
    name: backendUser?.name ?? backendUser?.nama ?? fallbackMember?.name ?? "-",
    phone: backendUser?.phone ?? backendUser?.no_hp ?? backendUser?.telepon ?? fallbackMember?.phone ?? "-",
    activePc:
      backendUser?.active_pc ??
      backendUser?.activePc ??
      backendBilling?.id_pc ??
      backendBilling?.pc_id ??
      fallbackMember?.activePc ??
      null,
    quota: extractQuotaFromBilling(backendBilling, fallbackMember?.quota)
  };
}

function openModal(title, fieldsHtml, onSubmit) {
  const backdrop = document.getElementById("modal-backdrop");
  if (!backdrop) return;
  const titleEl = document.getElementById("modal-title");
  if (titleEl) titleEl.textContent = title;
  const form = document.getElementById("modal-form");
  if (!form) return;
  form.innerHTML = fieldsHtml;
  backdrop.classList.remove("hidden");
  form.onsubmit = function(e) {
    e.preventDefault();
    onSubmit(new FormData(form));
    closeModal();
  };
}

function closeModal() {
  const backdrop = document.getElementById("modal-backdrop");
  if (backdrop) backdrop.classList.add("hidden");
}

document.addEventListener("DOMContentLoaded", function() {
  const cancelBtn = document.getElementById("modal-cancel");
  if (cancelBtn) cancelBtn.addEventListener("click", closeModal);

  const sess = getCurrentSession();
  const sessInfo = document.getElementById("session-info");
  if (sessInfo) {
    sessInfo.textContent = sess.role === "admin" ? `Admin (${sess.username})` : `Member (${sess.username})`;
  }

  const logoutBtn = document.getElementById("logout-btn");
  const logoutForm = document.getElementById("logout-form");
  if (logoutBtn && logoutForm) {
    logoutBtn.addEventListener("click", function() {
      sessionStorage.removeItem("warnet_user");
      sessionStorage.removeItem("warnet_role");
      logoutForm.submit();
    });
  }

  const loginForm = document.getElementById("login-form");
  if (loginForm) {
    loginForm.addEventListener("submit", function(e) {
      e.preventDefault();
      const loginUserEl = document.getElementById("login-username");
      const loginRoleEl = document.getElementById("login-role");
      const username = (loginUserEl ? loginUserEl.value.trim() : "") || "capuri";
      const role = loginRoleEl ? loginRoleEl.value : "user";
      sessionStorage.setItem("warnet_user", username);
      sessionStorage.setItem("warnet_role", role);
      window.location.href = role === "admin" ? "/admin/manajemen-pc" : "/denah-pc";
    });
  }

  if (document.getElementById("denah-grid")) initDenahPage();
  if (document.getElementById("billing-zones")) initBillingPage();
  if (document.getElementById("quota-commoner")) initProfilePage();
  if (document.getElementById("admin-pc-body")) initAdminPcPage();
  if (document.getElementById("admin-account-body")) initAdminAccountPage();
  if (document.getElementById("admin-zone-cards")) initAdminBillingPage();
});

function initDenahPage() {
  const filterBtns = document.querySelectorAll("[data-zone-filter]");
  filterBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      filterBtns.forEach(item => {
        item.classList.remove("btn-primary");
        item.classList.add("btn-secondary");
      });
      btn.classList.remove("btn-secondary");
      btn.classList.add("btn-primary");

      warnetStore.zoneFilter = btn.dataset.zoneFilter;
      renderDenah();
    });
  });

  const onlyAvail = document.getElementById("only-available-check");
  if (onlyAvail) {
    onlyAvail.addEventListener("change", e => {
      warnetStore.onlyAvailable = e.target.checked;
      renderDenah();
    });
  }
  renderDenah();
}

function renderDenah() {
  const availCount = warnetStore.pcs.filter(p => p.status === "Tersedia").length;
  const usedCount = warnetStore.pcs.filter(p => p.status === "Digunakan" || p.status === "Terkunci").length;
  const maintCount = warnetStore.pcs.filter(p => p.status === "Maintenance").length;
  const summaryEl = document.getElementById("denah-summary");
  if (summaryEl) {
    summaryEl.innerHTML = `
      <span class="badge badge-green">${availCount} Tersedia</span>
      <span class="badge badge-blue">${usedCount} Digunakan</span>
      <span class="badge badge-red">${maintCount} Maintenance</span>
    `;
  }

  const filtered = warnetStore.pcs.filter(pc => {
    const zone = getZoneById(pc.zoneId);
    const matchZone = warnetStore.zoneFilter === "ALL" || zone.name === warnetStore.zoneFilter;
    const matchAvail = !warnetStore.onlyAvailable || pc.status === "Tersedia";
    return matchZone && matchAvail;
  });

  const gridEl = document.getElementById("denah-grid");
  if (gridEl) {
    gridEl.innerHTML = filtered.map(pc => {
      const zone = getZoneById(pc.zoneId);
      const badgeClass = pc.status === "Tersedia"
        ? "badge-green"
        : pc.status === "Digunakan" || pc.status === "Terkunci"
          ? "badge-blue"
          : "badge-red";
      return `
        <div class="pc-card">
          <div class="pc-top">
            <span>${zone.name}</span>
            <span class="badge ${badgeClass}">${pc.status}</span>
          </div>
          <div class="pc-code">${pc.id}</div>
          <div class="pc-spec">${zone.cpu} • ${zone.gpu}</div>
          <div class="pc-spec mt-12">${pc.user ? "User: " + pc.user : "IP: " + pc.ip}</div>
        </div>
      `;
    }).join("");
  }
}

function getOrderDetails() {
  const zone = getZoneById(warnetStore.selectedZoneId);
  if (warnetStore.useCustomHours) {
    return {
      zone,
      label: `${zone.name} - Top-up (${warnetStore.customHours} Jam)`,
      total: zone.ratePerHour * warnetStore.customHours,
      minutes: warnetStore.customHours * 60
    };
  }
  const pkg = warnetStore.packages.find(p => p.id === warnetStore.selectedPackageId) || warnetStore.packages[0];
  return {
    zone,
    label: `${zone.name} - ${pkg.name} (${pkg.hours} Jam)`,
    total: pkg.price,
    minutes: pkg.hours * 60
  };
}

function initBillingPage() {
  const minusBtn = document.getElementById("custom-minus");
  const plusBtn = document.getElementById("custom-plus");
  const useCustomBtn = document.getElementById("use-custom-btn");
  const payBtn = document.getElementById("pay-billing-btn");

  if (minusBtn) {
    minusBtn.addEventListener("click", () => {
      if (warnetStore.customHours > 1) warnetStore.customHours -= 1;
      renderBilling();
    });
  }
  if (plusBtn) {
    plusBtn.addEventListener("click", () => {
      warnetStore.customHours += 1;
      renderBilling();
    });
  }
  if (useCustomBtn) {
    useCustomBtn.addEventListener("click", () => {
      warnetStore.useCustomHours = true;
      renderBilling();
    });
  }
  if (payBtn) {
    payBtn.addEventListener("click", () => {
      const member = getActiveMember();
      const order = getOrderDetails();
      const methodEl = document.getElementById("payment-method");
      const method = methodEl ? methodEl.value : "QRIS Dinamis";
      member.quota[order.zone.id] = (member.quota[order.zone.id] || 0) + order.minutes;
      warnetStore.transactions.unshift({
        id: "TX-" + Math.floor(100000 + Math.random() * 900000),
        member: member.username,
        time: "Baru saja",
        label: order.label,
        method,
        amount: order.total
      });
      window.location.href = "/profile";
    });
  }
  renderBilling();
}

function renderBilling() {
  const zonesEl = document.getElementById("billing-zones");
  if (zonesEl) {
    zonesEl.innerHTML = warnetStore.zones.map(z => `
      <div class="card selectable ${z.id === warnetStore.selectedZoneId ? "selected" : ""}" data-select-zone="${z.id}">
        <div style="font-weight:bold;">${z.name}</div>
        <div style="color:#22d3ee; font-weight:bold; margin-top:6px;">${formatRupiah(z.ratePerHour)} / jam</div>
        <div class="muted mt-12">${z.cpu}</div>
      </div>
    `).join("");
  }

  const pkgsEl = document.getElementById("billing-packages");
  if (pkgsEl) {
    pkgsEl.innerHTML = warnetStore.packages.filter(p => p.active).map(p => {
      const isSelected = !warnetStore.useCustomHours && p.id === warnetStore.selectedPackageId;
      return `
        <div class="card selectable ${isSelected ? "selected" : ""}" data-select-pkg="${p.id}">
          <div style="font-weight:bold;">${p.name}</div>
          <div class="muted mt-12">${p.hours} Jam</div>
          <div style="color:#34d399; font-weight:bold; margin-top:8px;">${formatRupiah(p.price)}</div>
        </div>
      `;
    }).join("");
  }

  const customHoursEl = document.getElementById("custom-hours");
  if (customHoursEl) customHoursEl.textContent = `${warnetStore.customHours} Jam`;

  document.querySelectorAll("[data-select-zone]").forEach(el => {
    el.addEventListener("click", () => {
      warnetStore.selectedZoneId = Number(el.dataset.selectZone);
      renderBilling();
    });
  });

  document.querySelectorAll("[data-select-pkg]").forEach(el => {
    el.addEventListener("click", () => {
      warnetStore.useCustomHours = false;
      warnetStore.selectedPackageId = Number(el.dataset.selectPkg);
      renderBilling();
    });
  });

  const order = getOrderDetails();
  const sumZoneEl = document.getElementById("summary-zone");
  const sumPkgEl = document.getElementById("summary-package");
  const sumTotalEl = document.getElementById("summary-total");
  if (sumZoneEl) sumZoneEl.textContent = order.zone.name;
  if (sumPkgEl) sumPkgEl.textContent = order.label;
  if (sumTotalEl) sumTotalEl.textContent = formatRupiah(order.total);
}

function initProfilePage() {
  const lockBtn = document.getElementById("remote-logout-btn");
  if (lockBtn) {
    lockBtn.addEventListener("click", () => {
      const member = getResolvedProfileMember();
      if (member && member.activePc) {
        const pc = warnetStore.pcs.find(p => p.id === member.activePc);
        if (pc) {
          pc.status = "Terkunci";
        }
        renderProfile();
      }
    });
  }
  renderProfile();
}

function renderProfile() {
  const member = getResolvedProfileMember();
  if (!member) return;

  const activePcObj = member.activePc
    ? warnetStore.pcs.find(p => p.id === member.activePc)
    : null;

  const profileNameEl = document.getElementById("profile-name");
  const profileMetaEl = document.getElementById("profile-meta");
  const quotaCommonerEl = document.getElementById("quota-commoner");
  const quotaNobleEl = document.getElementById("quota-noble");
  const quotaRoyaltyEl = document.getElementById("quota-royalty");
  const activeSessionEl = document.getElementById("active-session-text");
  const historyBodyEl = document.getElementById("user-history-body");

  if (profileNameEl) profileNameEl.textContent = member.name;
  if (profileMetaEl) profileMetaEl.textContent = `@${member.username} • ID: ${member.id} • ${member.phone}`;
  if (quotaCommonerEl) quotaCommonerEl.textContent = formatMinutes(member.quota[1]);
  if (quotaNobleEl) quotaNobleEl.textContent = formatMinutes(member.quota[2]);
  if (quotaRoyaltyEl) quotaRoyaltyEl.textContent = formatMinutes(member.quota[3]);

  if (activeSessionEl) {
    if (!member.activePc) {
      activeSessionEl.textContent = "Tidak ada sesi aktif";
    } else if (activePcObj && activePcObj.status === "Terkunci") {
      activeSessionEl.textContent = `Unit ${member.activePc} sedang terkunci`;
    } else {
      activeSessionEl.textContent = `Sedang terhubung di unit ${member.activePc}`;
    }
  }

  if (historyBodyEl) {
    const userTx = warnetStore.transactions.filter(t => t.member === member.username);
    if (userTx.length === 0) {
      historyBodyEl.innerHTML = `<tr><td colspan="5" class="muted">Belum ada transaksi billing</td></tr>`;
    } else {
      historyBodyEl.innerHTML = userTx.map(t => `
        <tr>
          <td>${t.id}</td>
          <td>${t.time}</td>
          <td>${t.label}</td>
          <td>${t.method}</td>
          <td>${formatRupiah(t.amount)}</td>
        </tr>
      `).join("");
    }
  }
}

function initAdminPcPage() {
  const searchEl = document.getElementById("admin-pc-search");
  const statusEl = document.getElementById("admin-pc-status");
  const addBtn = document.getElementById("open-add-pc-btn");

  if (searchEl) searchEl.addEventListener("input", renderAdminPc);
  if (statusEl) statusEl.addEventListener("change", renderAdminPc);
  if (addBtn) {
    addBtn.addEventListener("click", () => {
      const zoneOpts = warnetStore.zones.map(z => `<option value="${z.id}">${z.name}</option>`).join("");
      openModal("Tambah Komputer Baru", `
        <div class="form-group"><label>ID PC</label><input name="id" class="form-control" placeholder="PC-07" required></div>
        <div class="form-group"><label>Zona</label><select name="zoneId" class="form-control">${zoneOpts}</select></div>
        <div class="form-group"><label>IP Address</label><input name="ip" class="form-control" placeholder="192.168.1.107" required></div>
        <div class="form-group"><label>Status</label><select name="status" class="form-control"><option>Tersedia</option><option>Digunakan</option><option>Terkunci</option><option>Maintenance</option></select></div>
      `, fd => {
        warnetStore.pcs.push({
          id: fd.get("id").trim(),
          zoneId: Number(fd.get("zoneId")),
          ip: fd.get("ip").trim(),
          status: fd.get("status"),
          user: null
        });
        renderAdminPc();
      });
    });
  }
  renderAdminPc();
}

function renderAdminPc() {
  const total = warnetStore.pcs.length;
  const used = warnetStore.pcs.filter(p => p.status === "Digunakan" || p.status === "Terkunci").length;
  const avail = warnetStore.pcs.filter(p => p.status === "Tersedia").length;
  const maint = warnetStore.pcs.filter(p => p.status === "Maintenance").length;
  const statsEl = document.getElementById("admin-pc-stats");
  if (statsEl) {
    statsEl.innerHTML = `
      <div class="card"><div class="muted">Total PC</div><div class="stat-val">${total} Unit</div></div>
      <div class="card"><div class="muted">Sedang Digunakan</div><div class="stat-val">${used} Unit</div></div>
      <div class="card"><div class="muted">Tersedia</div><div class="stat-val">${avail} Unit</div></div>
      <div class="card"><div class="muted">Maintenance</div><div class="stat-val">${maint} Unit</div></div>
    `;
  }

  const searchEl = document.getElementById("admin-pc-search");
  const statusEl = document.getElementById("admin-pc-status");
  const q = searchEl ? searchEl.value.toLowerCase() : "";
  const st = statusEl ? statusEl.value : "ALL";
  const rows = warnetStore.pcs.filter(pc => {
    const matchStatus = st === "ALL" || pc.status === st;
    const matchQuery = !q || pc.id.toLowerCase().includes(q) || pc.ip.includes(q) || (pc.user || "").toLowerCase().includes(q);
    return matchStatus && matchQuery;
  });

  const bodyEl = document.getElementById("admin-pc-body");
  if (bodyEl) {
    bodyEl.innerHTML = rows.map(pc => {
      const zone = getZoneById(pc.zoneId);
      return `
        <tr>
          <td><strong>${pc.id}</strong></td>
          <td>${zone.name}</td>
          <td>${pc.ip}</td>
          <td>${pc.status}</td>
          <td>${pc.user || "-"}</td>
          <td>
            <button class="btn btn-secondary btn-sm" onclick="editPc('${pc.id}')">Edit</button>
            <button class="btn btn-danger btn-sm" onclick="deletePc('${pc.id}')">Hapus</button>
          </td>
        </tr>
      `;
    }).join("");
  }
}

window.editPc = function(pcId) {
  const pc = warnetStore.pcs.find(p => p.id === pcId);
  if (!pc) return;
  const zoneOpts = warnetStore.zones.map(z => `<option value="${z.id}" ${z.id === pc.zoneId ? "selected" : ""}>${z.name}</option>`).join("");
  openModal("Edit Komputer " + pc.id, `
    <div class="form-group"><label>Zona</label><select name="zoneId" class="form-control">${zoneOpts}</select></div>
    <div class="form-group"><label>IP Address</label><input name="ip" class="form-control" value="${pc.ip}" required></div>
    <div class="form-group"><label>Status</label><select name="status" class="form-control"><option ${pc.status === "Tersedia" ? "selected" : ""}>Tersedia</option><option ${pc.status === "Digunakan" ? "selected" : ""}>Digunakan</option><option ${pc.status === "Terkunci" ? "selected" : ""}>Terkunci</option><option ${pc.status === "Maintenance" ? "selected" : ""}>Maintenance</option></select></div>
  `, fd => {
    pc.zoneId = Number(fd.get("zoneId"));
    pc.ip = fd.get("ip").trim();
    pc.status = fd.get("status");
    renderAdminPc();
  });
};

window.deletePc = function(pcId) {
  warnetStore.pcs = warnetStore.pcs.filter(p => p.id !== pcId);
  renderAdminPc();
};

function initAdminAccountPage() {
  const searchEl = document.getElementById("admin-account-search");
  const addBtn = document.getElementById("open-add-account-btn");
  if (searchEl) searchEl.addEventListener("input", renderAdminAccounts);
  if (addBtn) {
    addBtn.addEventListener("click", () => {
      openModal("Registrasi Member Baru", `
        <div class="form-group"><label>Username</label><input name="username" class="form-control" required></div>
        <div class="form-group"><label>Nama Lengkap</label><input name="name" class="form-control" required></div>
        <div class="form-group"><label>No. Telepon</label><input name="phone" class="form-control" required></div>
      `, fd => {
        warnetStore.accounts.push({
          id: "NX-" + Math.floor(100000 + Math.random() * 900000),
          username: fd.get("username").trim(),
          name: fd.get("name").trim(),
          phone: fd.get("phone").trim(),
          status: "Aktif",
          quota: { 1: 0, 2: 0, 3: 0 },
          activePc: null
        });
        renderAdminAccounts();
      });
    });
  }
  renderAdminAccounts();
}

function renderAdminAccounts() {
  const searchEl = document.getElementById("admin-account-search");
  const q = searchEl ? searchEl.value.toLowerCase() : "";
  const list = warnetStore.accounts.filter(a =>
    !q || a.username.toLowerCase().includes(q) || a.name.toLowerCase().includes(q) || a.id.toLowerCase().includes(q)
  );
  const bodyEl = document.getElementById("admin-account-body");
  if (bodyEl) {
    bodyEl.innerHTML = list.map(a => `
      <tr>
        <td>${a.id}</td>
        <td><strong>${a.name}</strong><div class="muted">@${a.username}</div></td>
        <td>${a.phone}</td>
        <td>${formatMinutes(a.quota[1])}</td>
        <td>${formatMinutes(a.quota[2])}</td>
        <td>${formatMinutes(a.quota[3])}</td>
        <td>${a.status}</td>
        <td>
          <button class="btn btn-primary btn-sm" onclick="topupAccount('${a.username}')">+ Billing</button>
          <button class="btn btn-danger btn-sm" onclick="deleteAccount('${a.username}')">Hapus</button>
        </td>
      </tr>
    `).join("");
  }
}

window.topupAccount = function(username) {
  const acc = warnetStore.accounts.find(a => a.username === username);
  if (!acc) return;
  const zoneOpts = warnetStore.zones.map(z => `<option value="${z.id}">${z.name}</option>`).join("");
  openModal("Tambah Billing - " + acc.username, `
    <div class="form-group"><label>Zona</label><select name="zoneId" class="form-control">${zoneOpts}</select></div>
    <div class="form-group"><label>Durasi (Jam)</label><input name="hours" type="number" min="1" value="2" class="form-control" required></div>
  `, fd => {
    const zId = Number(fd.get("zoneId"));
    const hrs = Number(fd.get("hours"));
    acc.quota[zId] = (acc.quota[zId] || 0) + hrs * 60;
    renderAdminAccounts();
  });
};

window.deleteAccount = function(username) {
  warnetStore.accounts = warnetStore.accounts.filter(a => a.username !== username);
  renderAdminAccounts();
};

function initAdminBillingPage() {
  const addZoneBtn = document.getElementById("open-add-zone-btn");
  const addPkgBtn = document.getElementById("open-add-package-btn");

  if (addZoneBtn) {
    addZoneBtn.addEventListener("click", () => {
      openModal("Tambah Zona Baru", `
        <div class="form-group"><label>Nama Zona</label><input name="name" class="form-control" required></div>
        <div class="form-group"><label>Tarif per Jam (Rp)</label><input name="rate" type="number" min="1000" value="10000" class="form-control" required></div>
      `, fd => {
        const nextId = Math.max(0, ...warnetStore.zones.map(z => z.id)) + 1;
        warnetStore.zones.push({
          id: nextId,
          name: fd.get("name").trim(),
          ratePerHour: Number(fd.get("rate")),
          cpu: "Standard PC",
          gpu: "Dedicated GPU",
          ram: "16GB",
          monitor: "144Hz"
        });
        renderAdminBilling();
      });
    });
  }

  if (addPkgBtn) {
    addPkgBtn.addEventListener("click", () => {
      const zoneOpts = warnetStore.zones.map(z => `<option value="${z.id}">${z.name}</option>`).join("");
      openModal("Tambah Paket Billing", `
        <div class="form-group"><label>Nama Paket</label><input name="name" class="form-control" required></div>
        <div class="form-group"><label>Zona</label><select name="zoneId" class="form-control">${zoneOpts}</select></div>
        <div class="form-group"><label>Durasi (Jam)</label><input name="hours" type="number" min="1" value="3" class="form-control" required></div>
        <div class="form-group"><label>Harga (Rp)</label><input name="price" type="number" min="1000" value="20000" class="form-control" required></div>
      `, fd => {
        const nextId = Math.max(0, ...warnetStore.packages.map(p => p.id)) + 1;
        warnetStore.packages.push({
          id: nextId,
          zoneId: Number(fd.get("zoneId")),
          name: fd.get("name").trim(),
          hours: Number(fd.get("hours")),
          price: Number(fd.get("price")),
          active: true
        });
        renderAdminBilling();
      });
    });
  }

  renderAdminBilling();
}

window.editZone = function(zoneId) {
  const z = warnetStore.zones.find(item => item.id === zoneId);
  if (!z) return;
  openModal("Edit Tarif " + z.name, `
    <div class="form-group"><label>Nama Zona</label><input name="name" class="form-control" value="${z.name}" required></div>
    <div class="form-group"><label>Tarif per Jam (Rp)</label><input name="rate" type="number" min="1000" value="${z.ratePerHour}" class="form-control" required></div>
  `, fd => {
    z.name = fd.get("name").trim();
    z.ratePerHour = Number(fd.get("rate"));
    renderAdminBilling();
  });
};

window.togglePackage = function(pkgId) {
  const pkg = warnetStore.packages.find(p => p.id === pkgId);
  if (pkg) {
    pkg.active = !pkg.active;
    renderAdminBilling();
  }
};

window.deletePackage = function(pkgId) {
  warnetStore.packages = warnetStore.packages.filter(p => p.id !== pkgId);
  renderAdminBilling();
};

function renderAdminBilling() {
  const zoneCardsEl = document.getElementById("admin-zone-cards");
  if (zoneCardsEl) {
    zoneCardsEl.innerHTML = warnetStore.zones.map(z => `
      <div class="card">
        <div class="row-between">
          <strong>${z.name}</strong>
          <button class="btn btn-secondary btn-sm" onclick="editZone(${z.id})">Edit Tarif</button>
        </div>
        <div style="font-size:22px; font-weight:bold; color:#22d3ee; margin-top:10px;">${formatRupiah(z.ratePerHour)} / jam</div>
        <div class="muted mt-12">${z.cpu} • ${z.gpu}</div>
      </div>
    `).join("");
  }

  const pkgBodyEl = document.getElementById("admin-package-body");
  if (pkgBodyEl) {
    pkgBodyEl.innerHTML = warnetStore.packages.map(p => {
      const z = getZoneById(p.zoneId);
      return `
        <tr>
          <td>${p.name}</td>
          <td>${z.name}</td>
          <td>${p.hours} Jam</td>
          <td>${formatRupiah(p.price)}</td>
          <td>${p.active ? "Aktif" : "Nonaktif"}</td>
          <td>
            <button class="btn btn-secondary btn-sm" onclick="togglePackage(${p.id})">Toggle</button>
            <button class="btn btn-danger btn-sm" onclick="deletePackage(${p.id})">Hapus</button>
          </td>
        </tr>
      `;
    }).join("");
  }

  const txBodyEl = document.getElementById("admin-tx-body");
  if (txBodyEl) {
    txBodyEl.innerHTML = warnetStore.transactions.map(t => `
      <tr>
        <td>${t.id}</td>
        <td>${t.member}</td>
        <td>${t.time}</td>
        <td>${t.label}</td>
        <td>${t.method}</td>
        <td>${formatRupiah(t.amount)}</td>
      </tr>
    `).join("");
  }
}