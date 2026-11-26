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

function parseNumberSafe(val, fallback = 0) {
  if (typeof val === "number") return Number.isFinite(val) ? val : fallback;
  if (typeof val === "string") {
    const cleaned = val.replace(/[^\d.-]/g, "");
    if (!cleaned) return fallback;
    const parsed = Number(cleaned);
    return Number.isFinite(parsed) ? parsed : fallback;
  }
  return fallback;
}

function pickFirstValue(obj, paths, fallback = undefined) {
  if (!obj || typeof obj !== "object") return fallback;
  for (const path of paths) {
    const parts = path.split(".");
    let cur = obj;
    let found = true;
    for (const part of parts) {
      if (cur === null || cur === undefined || typeof cur !== "object" || !(part in cur)) {
        found = false;
        break;
      }
      cur = cur[part];
    }
    if (found && cur !== null && cur !== undefined && String(cur).trim() !== "") {
      return cur;
    }
  }
  return fallback;
}

function parseJsonAttr(el, attrName, fallback = null) {
  if (!el || !el.dataset || !el.dataset[attrName]) return fallback;
  try {
    const parsed = JSON.parse(el.dataset[attrName]);
    return parsed ?? fallback;
  } catch (_) {
    return fallback;
  }
}

function extractTextFromCandidate(val) {
  if (val === null || val === undefined) return "";
  if (typeof val === "object") {
    return String(
      val.username ??
      val.nama_akun ??
      val.nama_member ??
      val.nama_user ??
      val.nama_pelanggan ??
      val.nama_paket ??
      val.nama_bill ??
      val.nama_zona ??
      val.nama ??
      val.name ??
      val.label ??
      ""
    ).trim();
  }
  return String(val).trim();
}

function normalizeZones(rawZones) {
  if (!Array.isArray(rawZones) || rawZones.length === 0) return warnetStore.zones;
  return rawZones.map((z, idx) => {
    const zoneId = parseNumberSafe(pickFirstValue(z, ["id", "id_zona", "zona_id", "zone_id"], idx + 1), idx + 1);
    const fallbackZone = warnetStore.zones.find(item => item.id === zoneId) || warnetStore.zones[idx] || warnetStore.zones[0];
    const rawRate = pickFirstValue(z, [
      "ratePerHour",
      "rate_per_hour",
      "tarif_per_jam",
      "tarifPerJam",
      "tarif_jam",
      "harga_per_jam",
      "hargaPerJam",
      "harga_jam",
      "tarif",
      "harga",
      "rate",
      "price",
      "biaya_per_jam",
      "biaya",
      "zona.tarif_per_jam",
      "zona.harga_per_jam",
      "zona.tarif",
      "zona.harga"
    ], 0);
    const parsedRate = parseNumberSafe(rawRate, 0);
    return {
      id: zoneId,
      name: String(pickFirstValue(z, ["name", "nama_zona", "nama", "zone_name", "zona.nama_zona", "zona.name"], fallbackZone?.name || `Zona ${idx + 1}`)),
      ratePerHour: parsedRate > 0 ? parsedRate : (fallbackZone?.ratePerHour ?? 0),
      cpu: String(pickFirstValue(z, ["cpu", "spesifikasi_cpu", "spec_cpu"], fallbackZone?.cpu || "-")),
      gpu: String(pickFirstValue(z, ["gpu", "spesifikasi_gpu", "spec_gpu"], fallbackZone?.gpu || "-")),
      ram: String(pickFirstValue(z, ["ram", "spesifikasi_ram", "spec_ram"], fallbackZone?.ram || "-")),
      monitor: String(pickFirstValue(z, ["monitor", "spesifikasi_monitor", "spec_monitor"], fallbackZone?.monitor || "-"))
    };
  });
}

function normalizePcs(rawPcs) {
  if (!Array.isArray(rawPcs) || rawPcs.length === 0) return warnetStore.pcs;
  return rawPcs.map(pc => {
    const rawUser = pickFirstValue(pc, [
      "user_aktif",
      "active_user",
      "activeUser",
      "user",
      "username",
      "akun.username",
      "akun.nama",
      "akun.name",
      "member.username",
      "sesiBermain.akun.username",
      "sesiBermain.akun.nama",
      "sesiBermain.username",
      "sesi_aktif.akun.username",
      "sesi_aktif.username",
      "sesi.akun.username",
      "sesi.username"
    ], null);
    const resolvedUser = (typeof rawUser === "object" && rawUser !== null)
      ? (rawUser.username ?? rawUser.nama ?? rawUser.name ?? null)
      : rawUser;
    return {
      id: String(pickFirstValue(pc, ["id", "id_pc", "kode_pc", "nama_pc"], "PC")),
      zoneId: parseNumberSafe(pickFirstValue(pc, ["zoneId", "id_zona", "zona_id", "zone_id"], 1), 1),
      ip: String(pickFirstValue(pc, ["ip", "ip_address", "alamat_ip"], "-")),
      status: String(pickFirstValue(pc, ["status", "status_pc"], "Tersedia")),
      user: resolvedUser && String(resolvedUser).trim() !== "-" ? String(resolvedUser) : null
    };
  });
}

function normalizeAccounts(rawUsers) {
  if (!Array.isArray(rawUsers) || rawUsers.length === 0) return warnetStore.accounts;
  return rawUsers.map(u => ({
    id: pickFirstValue(u, ["id", "id_akun", "user_id"], "-"),
    username: String(pickFirstValue(u, ["username", "user_name"], "-")),
    name: String(pickFirstValue(u, ["name", "nama", "nama_lengkap"], "-")),
    phone: String(pickFirstValue(u, ["phone", "no_hp", "telepon", "no_telepon"], "-")),
    status: String(pickFirstValue(u, ["status"], "Aktif")),
    quota: {
      1: parseNumberSafe(pickFirstValue(u, ["quota.1", "commoner", "sisa_commoner", "quota_commoner", "saldo_commoner"], 0), 0),
      2: parseNumberSafe(pickFirstValue(u, ["quota.2", "noble", "sisa_noble", "quota_noble", "saldo_noble"], 0), 0),
      3: parseNumberSafe(pickFirstValue(u, ["quota.3", "royalty", "sisa_royalty", "quota_royalty", "saldo_royalty"], 0), 0)
    },
    activePc: pickFirstValue(u, ["activePc", "active_pc", "id_pc"], null)
  }));
}

function extractPriceFromBill(rawBill, fallbackPrice = 0) {
  const candidate = pickFirstValue(rawBill, [
    "price",
    "harga",
    "harga_paket",
    "harga_jual",
    "harga_satuan",
    "harga_per_jam",
    "tarif_per_jam",
    "tarif_paket",
    "tarif",
    "nominal_harga",
    "nominal_paket",
    "nominal",
    "total_harga",
    "total_biaya",
    "jumlah_bayar",
    "total",
    "biaya",
    "paket.harga",
    "paket.price",
    "paket.tarif",
    "paket.nominal",
    "bill.harga",
    "bill.price",
    "billing.harga",
    "billing.price",
    "pivot.harga",
    "pivot.price"
  ], null);
  if (candidate !== null && candidate !== undefined && String(candidate).trim() !== "") {
    const num = parseNumberSafe(candidate, NaN);
    if (Number.isFinite(num) && num > 0) {
      return num;
    }
    if (Number.isFinite(num) && num === 0 && (
      "price" in (rawBill || {}) ||
      "harga" in (rawBill || {}) ||
      "tarif" in (rawBill || {}) ||
      "nominal" in (rawBill || {})
    )) {
      return 0;
    }
  }
  return parseNumberSafe(fallbackPrice, 0);
}

function normalizeBills(rawBills) {
  if (!Array.isArray(rawBills) || rawBills.length === 0) return warnetStore.packages;
  return rawBills.map((p, idx) => {
    const fallbackPkg = warnetStore.packages[idx] || warnetStore.packages[0];
    const pkgNameRaw = pickFirstValue(p, [
      "name",
      "nama_paket",
      "nama_bill",
      "nama",
      "judul",
      "paket.nama_paket",
      "paket.name",
      "paket.nama",
      "bill.nama_paket",
      "bill.name"
    ], `Paket ${idx + 1}`);
    const pkgName = typeof pkgNameRaw === "object" && pkgNameRaw !== null
      ? (pkgNameRaw.nama_paket ?? pkgNameRaw.name ?? pkgNameRaw.nama ?? `Paket ${idx + 1}`)
      : String(pkgNameRaw);
    const matchedByName = warnetStore.packages.find(
      item => item.name.toLowerCase() === String(pkgName).toLowerCase()
    );
    const effectiveFallback = matchedByName || fallbackPkg;
    const rawHours = pickFirstValue(p, [
      "hours",
      "durasi_jam",
      "durasi",
      "jam",
      "jumlah_jam",
      "paket.durasi_jam",
      "paket.hours",
      "paket.durasi"
    ], effectiveFallback?.hours ?? 1);
    const parsedHours = parseNumberSafe(rawHours, effectiveFallback?.hours ?? 1);
    const parsedPrice = extractPriceFromBill(p, effectiveFallback?.price ?? 0);
    const activeRaw = pickFirstValue(p, ["active", "is_active", "aktif", "status_aktif", "status"], true);
    const isActive = typeof activeRaw === "string"
      ? !["0", "false", "nonaktif", "inactive", "tidak aktif"].includes(activeRaw.toLowerCase())
      : Boolean(activeRaw);
    return {
      id: parseNumberSafe(pickFirstValue(p, ["id", "id_paket", "id_bill", "paket_id", "bill_id"], idx + 1), idx + 1),
      zoneId: parseNumberSafe(
        pickFirstValue(p, ["zoneId", "id_zona", "zona_id", "zone_id", "zona.id_zona", "zona.id", "paket.id_zona"], effectiveFallback?.zoneId ?? 1),
        1
      ),
      name: String(pkgName),
      hours: parsedHours,
      price: parsedPrice,
      active: isActive
    };
  });
}

function normalizeTransactions(rawTx) {
  if (!Array.isArray(rawTx) || rawTx.length === 0) return warnetStore.transactions;

  const rootEl =
    document.getElementById("billing-page-root") ||
    document.getElementById("admin-billing-root") ||
    document.getElementById("profile-page-root");
  const zones = normalizeZones(parseJsonAttr(rootEl, "zones", null));
  const packages = normalizeBills(
    parseJsonAttr(rootEl, "bills", null) ?? parseJsonAttr(rootEl, "packages", null)
  );

  return rawTx.map(t => {
    const memberCandidates = [
      t.member,
      t.nama_member,
      t.member_name,
      t.username,
      t.nama_user,
      t.nama_akun,
      t.nama_pelanggan,
      t.akun,
      t.akun_pelanggan,
      t.akunPelanggan,
      t.pelanggan,
      t.user,
      t.nama,
      t.name
    ];

    let memberVal = "-";
    for (const cand of memberCandidates) {
      const text = extractTextFromCandidate(cand);
      if (text !== "" && text !== "-") {
        memberVal = text;
        break;
      }
    }

    const rawAccountId = pickFirstValue(
      t,
      [
        "id_akun_pelanggan",
        "id_akun",
        "akun_id",
        "pelanggan_id",
        "user_id",
        "member_id",
        "akun_pelanggan.id_akun",
        "akunPelanggan.id_akun",
        "akun.id_akun",
        "akun.id",
        "user.id"
      ],
      null
    );

    if (memberVal === "-" && rawAccountId !== null && String(rawAccountId).trim() !== "") {
      memberVal = `Member #${rawAccountId}`;
    }

    const timeVal = extractTextFromCandidate(
      pickFirstValue(t, [
        "time",
        "waktu",
        "waktu_transaksi",
        "tgl_transaksi",
        "tanggal_transaksi",
        "tanggal",
        "created_at",
        "updated_at"
      ], "-")
    ) || "-";

    const directLabel = extractTextFromCandidate(
      pickFirstValue(t, [
        "label",
        "zona_durasi",
        "zona_dan_durasi",
        "paket_durasi",
        "paket_dan_durasi",
        "nama_paket",
        "keterangan",
        "deskripsi",
        "catatan",
        "jenis_transaksi",
        "tipe_transaksi",
        "item"
      ], "")
    );

    const amountVal = pickFirstValue(t, [
      "amount",
      "total_bayar",
      "nominal",
      "total",
      "total_harga",
      "harga",
      "biaya"
    ], 0);
    const parsedAmount = parseNumberSafe(amountVal, 0);

    let builtLabel = directLabel;
    if (!builtLabel || builtLabel === "-") {
      const matchedPkgId = parseNumberSafe(
        pickFirstValue(t, ["id_paket", "paket_id", "id_bill", "bill_id"], NaN),
        NaN
      );
      let matchedPkg = Number.isFinite(matchedPkgId)
        ? packages.find(p => Number(p.id) === Number(matchedPkgId))
        : null;

      if (!matchedPkg && parsedAmount > 0) {
        matchedPkg = packages.find(p => Number(p.price) === Number(parsedAmount)) || null;
      }

      const matchedZoneId = parseNumberSafe(
        pickFirstValue(
          t,
          ["id_zona", "zona_id", "zone_id", "paket.id_zona", "paket_billing.id_zona"],
          matchedPkg?.zoneId ?? NaN
        ),
        NaN
      );
      const matchedZone = Number.isFinite(matchedZoneId)
        ? zones.find(z => Number(z.id) === Number(matchedZoneId))
        : null;

      const zoneName = extractTextFromCandidate(
        pickFirstValue(t, [
          "nama_zona",
          "zona",
          "zone",
          "paket.zona",
          "paket_billing.zona",
          "paketBilling.zona",
          "bill.zona"
        ], matchedZone?.name || "")
      );

      const pkgName = extractTextFromCandidate(
        pickFirstValue(t, [
          "nama_paket",
          "nama_bill",
          "paket",
          "paket_billing",
          "paketBilling",
          "bill",
          "billing"
        ], matchedPkg?.name || "")
      );

      const durasiJam = extractTextFromCandidate(
        pickFirstValue(t, [
          "durasi_jam",
          "durasi",
          "hours",
          "jam",
          "jumlah_jam",
          "paket.durasi_jam",
          "paket.hours",
          "paket_billing.durasi_jam",
          "paketBilling.durasi_jam",
          "bill.durasi_jam"
        ], matchedPkg?.hours ?? "")
      );

      const durasiMenit = extractTextFromCandidate(
        pickFirstValue(t, [
          "durasi_menit",
          "menit",
          "minutes",
          "paket.durasi_menit",
          "paket_billing.durasi_menit"
        ], "")
      );

      let durText = "";
      if (durasiJam !== "") {
        durText = durasiJam.toLowerCase().includes("jam") ? ` (${durasiJam})` : ` (${durasiJam} Jam)`;
      } else if (durasiMenit !== "") {
        durText = durasiMenit.toLowerCase().includes("menit") ? ` (${durasiMenit})` : ` (${durasiMenit} Menit)`;
      }

      if (zoneName && pkgName) {
        builtLabel = `${zoneName} - ${pkgName}${durText}`;
      } else if (pkgName) {
        builtLabel = `${pkgName}${durText}`;
      } else if (zoneName) {
        builtLabel = `${zoneName}${durText}`;
      } else if (durText) {
        builtLabel = durText.replace(/^\s*\(|\)\s*$/g, "");
      } else if (parsedAmount > 0) {
        const matchedZoneByRate = zones.find(
          z => Number(z.ratePerHour) > 0 && parsedAmount % Number(z.ratePerHour) === 0
        );
        if (matchedZoneByRate) {
          const estHours = parsedAmount / Number(matchedZoneByRate.ratePerHour);
          builtLabel = `${matchedZoneByRate.name} - Top-up (${estHours} Jam)`;
        } else {
          builtLabel = "Top-up Billing";
        }
      } else {
        builtLabel = "-";
      }
    }

    const methodVal = extractTextFromCandidate(
      pickFirstValue(t, [
        "method",
        "metode_pembayaran",
        "metode",
        "payment_method",
        "channel_pembayaran"
      ], "-")
    ) || "-";

    return {
      id: String(pickFirstValue(t, ["id", "id_transaksi", "kode_transaksi", "no_transaksi", "transaction_id"], "-")),
      member: String(memberVal),
      ownerId: rawAccountId,
      ownerUsername: pickFirstValue(t, ["username", "member_username", "akun.username", "user.username"], null),
      time: String(timeVal),
      label: String(builtLabel),
      method: String(methodVal),
      amount: parsedAmount
    };
  });
}

function getResolvedDenahData() {
  const rootEl = document.getElementById("denah-page-root");
  const pcs = normalizePcs(parseJsonAttr(rootEl, "pcs", null));
  const zones = normalizeZones(parseJsonAttr(rootEl, "zones", null));
  return { pcs, zones };
}

function getResolvedAdminPcData() {
  const rootEl = document.getElementById("admin-pc-root");
  const pcs = normalizePcs(parseJsonAttr(rootEl, "pcs", null));
  const zones = normalizeZones(parseJsonAttr(rootEl, "zones", null));
  return { pcs, zones };
}

function getResolvedAdminAccountData() {
  const rootEl = document.getElementById("admin-account-root");
  const accounts = normalizeAccounts(
    parseJsonAttr(rootEl, "users", null) ?? parseJsonAttr(rootEl, "accounts", null)
  );
  const zones = normalizeZones(parseJsonAttr(rootEl, "zones", null));
  return { accounts, zones };
}

function getResolvedBillingData() {
  const rootEl =
    document.getElementById("billing-page-root") ||
    document.getElementById("admin-billing-root");
  const zones = normalizeZones(parseJsonAttr(rootEl, "zones", null));
  const packages = normalizeBills(
    parseJsonAttr(rootEl, "bills", null) ?? parseJsonAttr(rootEl, "packages", null)
  );
  const transactions = normalizeTransactions(parseJsonAttr(rootEl, "transactions", null));
  return { zones, packages, transactions };
}

function getResolvedProfileTransactions() {
  const rootEl = document.getElementById("profile-page-root");
  const rawTx = parseJsonAttr(rootEl, "transactions", null);
  if (Array.isArray(rawTx)) {
    if (rawTx.length === 0) return [];
    return normalizeTransactions(rawTx).map((item, idx) => ({
      ...item,
      _fromProfileBackend: true,
      _raw: rawTx[idx] || {}
    }));
  }
  return warnetStore.transactions.map(item => ({
    ...item,
    _fromProfileBackend: false
  }));
}

function getZoneById(id) {
  const { zones } = getResolvedDenahData();
  const billingZones = getResolvedBillingData().zones;
  const adminPcZones = getResolvedAdminPcData().zones;
  const adminAccZones = getResolvedAdminAccountData().zones;
  const pool = [zones, billingZones, adminPcZones, adminAccZones].find(
    list => Array.isArray(list) && list.some(z => z.id === Number(id))
  );
  if (pool) {
    return pool.find(z => z.id === Number(id)) || pool[0];
  }
  return zones[0] || warnetStore.zones[0];
}

function getBackendProfilePayload() {
  const rootEl = document.getElementById("profile-page-root");
  let userData = window.warnetUserData || null;
  let billingData = window.warnetBillingData || null;
  if (rootEl) {
    const parsedUser = parseJsonAttr(rootEl, "user", null);
    const parsedBilling = parseJsonAttr(rootEl, "billing", null);
    if (parsedUser !== null) userData = parsedUser;
    if (parsedBilling !== null) billingData = parsedBilling;
  }
  if (!billingData && userData && typeof userData === "object") {
    billingData = userData.saldoWaktu ?? userData.saldo_waktu ?? null;
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
      const zoneVal = String(
        item.zona ?? item.zone ?? item.nama_zona ?? item.zona?.nama_zona ?? item.zona?.name ?? item.id_zona ?? ""
      ).toLowerCase();
      const mins = parseNumberSafe(item.sisa_menit ?? item.menit ?? item.kuota ?? item.saldo ?? item.balance ?? 0, 0);
      if (zoneVal === "commoner" || zoneVal === "1") result[1] = mins;
      if (zoneVal === "noble" || zoneVal === "2") result[2] = mins;
      if (zoneVal === "royalty" || zoneVal === "3") result[3] = mins;
    });
    return result;
  }
  if (typeof billingData === "object") {
    return {
      1: parseNumberSafe(
        billingData.commoner ??
        billingData.sisa_commoner ??
        billingData.quota_commoner ??
        billingData[1] ??
        defaultQuota[1],
        0
      ),
      2: parseNumberSafe(
        billingData.noble ??
        billingData.sisa_noble ??
        billingData.quota_noble ??
        billingData[2] ??
        defaultQuota[2],
        0
      ),
      3: parseNumberSafe(
        billingData.royalty ??
        billingData.sisa_royalty ??
        billingData.quota_royalty ??
        billingData[3] ??
        defaultQuota[3],
        0
      )
    };
  }
  return defaultQuota;
}

function resolveActivePcFromUser(backendUser, backendBilling, fallbackPc) {
  const sesi = backendUser?.sesiBermain ?? backendUser?.sesi_bermain ?? null;
  let sesiPc = null;
  if (Array.isArray(sesi)) {
    const activeItem = sesi.find(item => {
      const st = String(item?.status ?? "").toLowerCase();
      const endAt = item?.waktu_selesai ?? item?.selesai ?? item?.end_at ?? null;
      return ["aktif", "berjalan", "digunakan", "terkunci", "active"].includes(st) || endAt === null;
    }) || sesi[sesi.length - 1];
    sesiPc = activeItem?.id_pc ?? activeItem?.pc_id ?? activeItem?.pc?.id_pc ?? activeItem?.pc?.id ?? null;
  } else if (sesi && typeof sesi === "object") {
    sesiPc = sesi.id_pc ?? sesi.pc_id ?? sesi.pc?.id_pc ?? sesi.pc?.id ?? null;
  }
  return (
    sesiPc ??
    backendUser?.active_pc ??
    backendUser?.activePc ??
    backendUser?.id_pc ??
    backendBilling?.id_pc ??
    backendBilling?.pc_id ??
    fallbackPc ??
    null
  );
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
    activePc: resolveActivePcFromUser(backendUser, backendBilling, fallbackMember?.activePc),
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
      const loginUserEl = document.getElementById("login-username");
      const username = (loginUserEl ? loginUserEl.value.trim() : "") || "capuri";
      const role = username.toLowerCase() === "admin" ? "admin" : "user";
      sessionStorage.setItem("warnet_user", username);
      sessionStorage.setItem("warnet_role", role);
      if (!loginForm.getAttribute("action")) {
        e.preventDefault();
        window.location.href = role === "admin" ? "/admin/manajemen-pc" : "/denah-pc";
      }
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
  const { pcs } = getResolvedDenahData();
  const availCount = pcs.filter(p => String(p.status).toLowerCase() === "tersedia").length;
  const usedCount = pcs.filter(p => ["digunakan", "terkunci"].includes(String(p.status).toLowerCase())).length;
  const maintCount = pcs.filter(p => String(p.status).toLowerCase() === "maintenance").length;
  const summaryEl = document.getElementById("denah-summary");
  if (summaryEl) {
    summaryEl.innerHTML = `
      <span class="badge badge-green">${availCount} Tersedia</span>
      <span class="badge badge-blue">${usedCount} Digunakan</span>
      <span class="badge badge-red">${maintCount} Maintenance</span>
    `;
  }
  const filtered = pcs.filter(pc => {
    const zone = getZoneById(pc.zoneId);
    const matchZone = warnetStore.zoneFilter === "ALL" || zone.name === warnetStore.zoneFilter;
    const matchAvail = !warnetStore.onlyAvailable || String(pc.status).toLowerCase() === "tersedia";
    return matchZone && matchAvail;
  });
  const gridEl = document.getElementById("denah-grid");
  if (gridEl) {
    gridEl.innerHTML = filtered.map(pc => {
      const zone = getZoneById(pc.zoneId);
      const stLower = String(pc.status).toLowerCase();
      const badgeClass = stLower === "tersedia"
        ? "badge-green"
        : ["digunakan", "terkunci"].includes(stLower)
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
  const { zones, packages } = getResolvedBillingData();
  const zone = zones.find(z => z.id === Number(warnetStore.selectedZoneId)) || zones[0] || warnetStore.zones[0];
  if (warnetStore.useCustomHours) {
    return {
      zone,
      pkg: null,
      label: `${zone.name} - Top-up (${warnetStore.customHours} Jam)`,
      total: zone.ratePerHour * warnetStore.customHours,
      minutes: warnetStore.customHours * 60
    };
  }
  const activePkgs = packages.filter(p => p.active);
  const pkg =
    activePkgs.find(p => p.id === Number(warnetStore.selectedPackageId)) ||
    activePkgs[0] ||
    packages[0] ||
    warnetStore.packages[0];
  return {
    zone,
    pkg,
    label: `${zone.name} - ${pkg.name} (${pkg.hours} Jam)`,
    total: pkg.price,
    minutes: pkg.hours * 60
  };
}

function initBillingPage() {
  const { zones, packages } = getResolvedBillingData();
  if (zones.length > 0 && !zones.some(z => z.id === Number(warnetStore.selectedZoneId))) {
    warnetStore.selectedZoneId = zones[0].id;
  }
  const activePkgs = packages.filter(p => p.active);
  if (activePkgs.length > 0 && !activePkgs.some(p => p.id === Number(warnetStore.selectedPackageId))) {
    warnetStore.selectedPackageId = activePkgs[0].id;
  }
  const minusBtn = document.getElementById("custom-minus");
  const plusBtn = document.getElementById("custom-plus");
  const useCustomBtn = document.getElementById("use-custom-btn");
  const payBtn = document.getElementById("pay-billing-btn");
  const billingForm = payBtn ? payBtn.closest("form") : null;
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
    payBtn.addEventListener("click", (e) => {
      syncBillingHiddenInputs();
      if (!billingForm || !billingForm.getAttribute("action")) {
        if (e && typeof e.preventDefault === "function") e.preventDefault();
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
      }
    });
  }
  renderBilling();
}

function syncBillingHiddenInputs() {
  const order = getOrderDetails();
  const zoneInput = document.getElementById("billing-zone-id");
  const pkgInput = document.getElementById("billing-package-id");
  const hoursInput = document.getElementById("billing-custom-hours");
  const useCustomInput = document.getElementById("billing-use-custom");
  if (zoneInput) zoneInput.value = order.zone.id;
  if (pkgInput) pkgInput.value = warnetStore.useCustomHours || !order.pkg ? "" : order.pkg.id;
  if (hoursInput) hoursInput.value = warnetStore.useCustomHours ? warnetStore.customHours : (order.pkg?.hours || 1);
  if (useCustomInput) useCustomInput.value = warnetStore.useCustomHours ? "1" : "0";
}

function renderBilling() {
  const { zones, packages } = getResolvedBillingData();
  const zonesEl = document.getElementById("billing-zones");
  if (zonesEl) {
    zonesEl.innerHTML = zones.map(z => `
      <div class="card selectable ${z.id === Number(warnetStore.selectedZoneId) ? "selected" : ""}" data-select-zone="${z.id}">
        <div style="font-weight:bold;">${z.name}</div>
        <div style="color:#22d3ee; font-weight:bold; margin-top:6px;">${formatRupiah(z.ratePerHour)} / jam</div>
        <div class="muted mt-12">${z.cpu}</div>
      </div>
    `).join("");
  }
  const pkgsEl = document.getElementById("billing-packages");
  if (pkgsEl) {
    pkgsEl.innerHTML = packages.filter(p => p.active).map(p => {
      const isSelected = !warnetStore.useCustomHours && p.id === Number(warnetStore.selectedPackageId);
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
  syncBillingHiddenInputs();
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
  const { pcs } = getResolvedDenahData();
  const activePcObj = member.activePc
    ? pcs.find(p => p.id === member.activePc)
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
    } else if (activePcObj && String(activePcObj.status).toLowerCase() === "terkunci") {
      activeSessionEl.textContent = `Unit ${member.activePc} sedang terkunci`;
    } else {
      activeSessionEl.textContent = `Sedang terhubung di unit ${member.activePc}`;
    }
  }
  if (historyBodyEl) {
    const allTx = getResolvedProfileTransactions();
    const isFromProfileBackend = allTx.length > 0 && allTx[0]._fromProfileBackend;
    const targetUsername = String(member.username || "").trim().toLowerCase();
    const targetId = String(member.id ?? "").trim().toLowerCase();
    const userTx = allTx.filter(t => {
      const candidates = [
        t.member,
        t.ownerUsername,
        t.ownerId,
        t._raw?.username,
        t._raw?.id_akun,
        t._raw?.id_akun_pelanggan,
        t._raw?.user_id
      ]
        .filter(v => v !== null && v !== undefined && String(v).trim() !== "" && String(v).trim() !== "-")
        .map(v => String(v).trim().toLowerCase());
      if (candidates.length === 0) {
        return Boolean(isFromProfileBackend);
      }
      const matchUser = targetUsername && candidates.includes(targetUsername);
      const matchId = targetId && targetId !== "-" && candidates.includes(targetId);
      return matchUser || matchId;
    });
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
      const { zones } = getResolvedAdminPcData();
      const zoneOpts = zones.map(z => `<option value="${z.id}">${z.name}</option>`).join("");
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

function renderAdminPcStatsOnly() {
  const { pcs } = getResolvedAdminPcData();
  const statsEl = document.getElementById("admin-pc-stats");
  if (!statsEl) return;
  const total = pcs.length;
  const used = pcs.filter(p => ["digunakan", "terkunci"].includes(String(p.status).toLowerCase())).length;
  const avail = pcs.filter(p => String(p.status).toLowerCase() === "tersedia").length;
  const maint = pcs.filter(p => String(p.status).toLowerCase() === "maintenance").length;
  statsEl.innerHTML = `
    <div class="card"><div class="muted">Total PC</div><div class="stat-val">${total} Unit</div></div>
    <div class="card"><div class="muted">Sedang Digunakan</div><div class="stat-val">${used} Unit</div></div>
    <div class="card"><div class="muted">Tersedia</div><div class="stat-val">${avail} Unit</div></div>
    <div class="card"><div class="muted">Maintenance</div><div class="stat-val">${maint} Unit</div></div>
  `;
}

function filterAdminPcTableRows() {
  const searchEl = document.getElementById("admin-pc-search");
  const statusEl = document.getElementById("admin-pc-status");
  const q = searchEl ? searchEl.value.trim().toLowerCase() : "";
  const st = statusEl ? statusEl.value : "ALL";
  const bodyEl = document.getElementById("admin-pc-body");
  if (!bodyEl) return;
  const rows = bodyEl.querySelectorAll("tr");
  rows.forEach(tr => {
    const text = tr.textContent.toLowerCase();
    const matchQuery = !q || text.includes(q);
    const matchStatus = st === "ALL" || text.includes(String(st).toLowerCase());
    tr.style.display = matchQuery && matchStatus ? "" : "none";
  });
}

function renderAdminPc() {
  renderAdminPcStatsOnly();
  filterAdminPcTableRows();
}

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
  const q = searchEl ? searchEl.value.trim().toLowerCase() : "";
  const bodyEl = document.getElementById("admin-account-body");
  if (bodyEl) {
    const rows = bodyEl.querySelectorAll("tr");
    rows.forEach(tr => {
      const text = tr.textContent.toLowerCase();
      tr.style.display = !q || text.includes(q) ? "" : "none";
    });
  }
}

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
      const { zones } = getResolvedBillingData();
      const zoneOpts = zones.map(z => `<option value="${z.id}">${z.name}</option>`).join("");
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
  const { zones, packages, transactions } = getResolvedBillingData();
  const zoneCardsEl = document.getElementById("admin-zone-cards");
  if (zoneCardsEl) {
    zoneCardsEl.innerHTML = zones.map(z => `
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
    pkgBodyEl.innerHTML = packages.map(p => {
      const z = zones.find(item => item.id === Number(p.zoneId)) || zones[0] || warnetStore.zones[0];
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
    txBodyEl.innerHTML = transactions.map(t => `
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
