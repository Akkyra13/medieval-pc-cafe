--
-- PostgreSQL database dump
--

\restrict nA9KWtAY2dBVygkdIGYI7ZuIfLGhKPJmkANTcFnPe1E5RB6LmWap81GWrSz6fkE

-- Dumped from database version 18.4
-- Dumped by pg_dump version 18.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: akun; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.akun (
    id_akun integer NOT NULL,
    id_akses integer NOT NULL,
    username character varying(20) NOT NULL,
    password character varying(255) NOT NULL,
    nama character varying(255) NOT NULL,
    notelp character varying(20),
    saldo_deposit integer DEFAULT 0
);


ALTER TABLE public.akun OWNER TO postgres;

--
-- Name: akun_id_akun_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.akun_id_akun_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.akun_id_akun_seq OWNER TO postgres;

--
-- Name: akun_id_akun_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.akun_id_akun_seq OWNED BY public.akun.id_akun;


--
-- Name: detail_transaksi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detail_transaksi (
    id_detail integer NOT NULL,
    id_transaksi character varying(10) NOT NULL,
    id_zona integer NOT NULL,
    durasi_beli_menit integer NOT NULL,
    subtotal_harga integer NOT NULL
);


ALTER TABLE public.detail_transaksi OWNER TO postgres;

--
-- Name: detail_transaksi_id_detail_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.detail_transaksi_id_detail_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.detail_transaksi_id_detail_seq OWNER TO postgres;

--
-- Name: detail_transaksi_id_detail_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.detail_transaksi_id_detail_seq OWNED BY public.detail_transaksi.id_detail;


--
-- Name: hak_akses; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.hak_akses (
    id_akses integer NOT NULL,
    nama_hak_akses character varying(20) NOT NULL
);


ALTER TABLE public.hak_akses OWNER TO postgres;

--
-- Name: hak_akses_id_akses_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.hak_akses_id_akses_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.hak_akses_id_akses_seq OWNER TO postgres;

--
-- Name: hak_akses_id_akses_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.hak_akses_id_akses_seq OWNED BY public.hak_akses.id_akses;


--
-- Name: paket_billing; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.paket_billing (
    id_paket integer NOT NULL,
    id_zona integer NOT NULL,
    nama_paket character varying(50) NOT NULL,
    durasi_jam integer NOT NULL,
    harga_paket integer NOT NULL,
    status_aktif boolean DEFAULT true
);


ALTER TABLE public.paket_billing OWNER TO postgres;

--
-- Name: paket_billing_id_paket_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.paket_billing_id_paket_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.paket_billing_id_paket_seq OWNER TO postgres;

--
-- Name: paket_billing_id_paket_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.paket_billing_id_paket_seq OWNED BY public.paket_billing.id_paket;


--
-- Name: pc; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pc (
    id_pc character(5) NOT NULL,
    id_zona integer NOT NULL,
    ip_address character varying(15),
    status_pc character varying(20)
);


ALTER TABLE public.pc OWNER TO postgres;

--
-- Name: saldo_waktu; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.saldo_waktu (
    id_akun integer NOT NULL,
    id_zona integer NOT NULL,
    sisa_waktu_menit integer DEFAULT 0
);


ALTER TABLE public.saldo_waktu OWNER TO postgres;

--
-- Name: sesi_bermain; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sesi_bermain (
    id_sesi integer NOT NULL,
    id_pc character(5) NOT NULL,
    id_akun integer NOT NULL,
    waktu_login timestamp without time zone NOT NULL,
    waktu_logout timestamp without time zone,
    durasi_terpakai_menit integer
);


ALTER TABLE public.sesi_bermain OWNER TO postgres;

--
-- Name: sesi_bermain_id_sesi_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sesi_bermain_id_sesi_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sesi_bermain_id_sesi_seq OWNER TO postgres;

--
-- Name: sesi_bermain_id_sesi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sesi_bermain_id_sesi_seq OWNED BY public.sesi_bermain.id_sesi;


--
-- Name: transaksi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.transaksi (
    id_transaksi character varying(10) NOT NULL,
    id_akun_pelanggan integer NOT NULL,
    id_akun_kasir integer,
    waktu_transaksi timestamp without time zone NOT NULL,
    total_bayar integer NOT NULL,
    metode_pembayaran character varying(20),
    status_pembayaran character varying(15),
    kode_referensi character varying(50)
);


ALTER TABLE public.transaksi OWNER TO postgres;

--
-- Name: zona; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.zona (
    id_zona integer NOT NULL,
    nama_zona character varying(20) NOT NULL,
    spek_cpu character varying(255),
    spek_gpu character varying(255),
    spek_ram character varying(255),
    spek_monitor character varying(255)
);


ALTER TABLE public.zona OWNER TO postgres;

--
-- Name: zona_id_zona_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.zona_id_zona_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.zona_id_zona_seq OWNER TO postgres;

--
-- Name: zona_id_zona_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.zona_id_zona_seq OWNED BY public.zona.id_zona;


--
-- Name: akun id_akun; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.akun ALTER COLUMN id_akun SET DEFAULT nextval('public.akun_id_akun_seq'::regclass);


--
-- Name: detail_transaksi id_detail; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detail_transaksi ALTER COLUMN id_detail SET DEFAULT nextval('public.detail_transaksi_id_detail_seq'::regclass);


--
-- Name: hak_akses id_akses; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hak_akses ALTER COLUMN id_akses SET DEFAULT nextval('public.hak_akses_id_akses_seq'::regclass);


--
-- Name: paket_billing id_paket; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paket_billing ALTER COLUMN id_paket SET DEFAULT nextval('public.paket_billing_id_paket_seq'::regclass);


--
-- Name: sesi_bermain id_sesi; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sesi_bermain ALTER COLUMN id_sesi SET DEFAULT nextval('public.sesi_bermain_id_sesi_seq'::regclass);


--
-- Name: zona id_zona; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zona ALTER COLUMN id_zona SET DEFAULT nextval('public.zona_id_zona_seq'::regclass);


--
-- Data for Name: akun; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.akun (id_akun, id_akses, username, password, nama, notelp, saldo_deposit) VALUES (1, 1, 'admin_fahri', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'OP Fahri (Admin)', '081200001111', 0);
INSERT INTO public.akun (id_akun, id_akses, username, password, nama, notelp, saldo_deposit) VALUES (2, 2, 'kasir_renata', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'OP Renata (Shift Pagi)', '081200002222', 0);
INSERT INTO public.akun (id_akun, id_akses, username, password, nama, notelp, saldo_deposit) VALUES (3, 3, 'capuri', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Oguri "Devour" Cap', '081299881122', 45000);
INSERT INTO public.akun (id_akun, id_akses, username, password, nama, notelp, saldo_deposit) VALUES (4, 3, 'viper987', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alex "Viper" Pratama', '081234567890', 15000);
INSERT INTO public.akun (id_akun, id_akses, username, password, nama, notelp, saldo_deposit) VALUES (5, 3, 'waylay1023', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Waylay Pro', '084823484583', 120000);


--
-- Data for Name: detail_transaksi; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.detail_transaksi (id_detail, id_transaksi, id_zona, durasi_beli_menit, subtotal_harga) VALUES (1, 'NX-9938210', 2, 120, 24000);
INSERT INTO public.detail_transaksi (id_detail, id_transaksi, id_zona, durasi_beli_menit, subtotal_harga) VALUES (2, 'NX-9934112', 1, 300, 30000);
INSERT INTO public.detail_transaksi (id_detail, id_transaksi, id_zona, durasi_beli_menit, subtotal_harga) VALUES (3, 'NX-9921094', 1, 120, 14000);
INSERT INTO public.detail_transaksi (id_detail, id_transaksi, id_zona, durasi_beli_menit, subtotal_harga) VALUES (4, 'NX-9908842', 3, 180, 75000);


--
-- Data for Name: hak_akses; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.hak_akses (id_akses, nama_hak_akses) VALUES (1, 'Admin');
INSERT INTO public.hak_akses (id_akses, nama_hak_akses) VALUES (2, 'Kasir');
INSERT INTO public.hak_akses (id_akses, nama_hak_akses) VALUES (3, 'Pelanggan');


--
-- Data for Name: paket_billing; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.paket_billing (id_paket, id_zona, nama_paket, durasi_jam, harga_paket, status_aktif) VALUES (1, 1, 'Paket Santai Commoner', 2, 14000, true);
INSERT INTO public.paket_billing (id_paket, id_zona, nama_paket, durasi_jam, harga_paket, status_aktif) VALUES (2, 1, 'Paket Mabar Commoner', 5, 30000, true);
INSERT INTO public.paket_billing (id_paket, id_zona, nama_paket, durasi_jam, harga_paket, status_aktif) VALUES (3, 1, 'Paket Begadang Commoner', 8, 45000, true);
INSERT INTO public.paket_billing (id_paket, id_zona, nama_paket, durasi_jam, harga_paket, status_aktif) VALUES (4, 2, 'Noble Pass 2 Jam', 2, 24000, true);
INSERT INTO public.paket_billing (id_paket, id_zona, nama_paket, durasi_jam, harga_paket, status_aktif) VALUES (5, 2, 'Noble Push Rank 5 Jam', 5, 55000, true);
INSERT INTO public.paket_billing (id_paket, id_zona, nama_paket, durasi_jam, harga_paket, status_aktif) VALUES (6, 3, 'Royal Stream Pass 3 Jam', 3, 75000, true);
INSERT INTO public.paket_billing (id_paket, id_zona, nama_paket, durasi_jam, harga_paket, status_aktif) VALUES (7, 3, 'Paket Promo Lama (Nonaktif)', 4, 80000, false);


--
-- Data for Name: pc; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.pc (id_pc, id_zona, ip_address, status_pc) VALUES ('PC-01', 1, '192.168.1.101', 'Tersedia');
INSERT INTO public.pc (id_pc, id_zona, ip_address, status_pc) VALUES ('PC-02', 1, '192.168.1.102', 'Digunakan');
INSERT INTO public.pc (id_pc, id_zona, ip_address, status_pc) VALUES ('PC-03', 1, '192.168.1.103', 'Tersedia');
INSERT INTO public.pc (id_pc, id_zona, ip_address, status_pc) VALUES ('NB-01', 2, '192.168.1.201', 'Tersedia');
INSERT INTO public.pc (id_pc, id_zona, ip_address, status_pc) VALUES ('NB-04', 2, '192.168.1.204', 'Digunakan');
INSERT INTO public.pc (id_pc, id_zona, ip_address, status_pc) VALUES ('NB-05', 2, '192.168.1.205', 'Maintenance');
INSERT INTO public.pc (id_pc, id_zona, ip_address, status_pc) VALUES ('RY-01', 3, '192.168.1.301', 'Tersedia');
INSERT INTO public.pc (id_pc, id_zona, ip_address, status_pc) VALUES ('RY-02', 3, '192.168.1.302', 'Digunakan');


--
-- Data for Name: saldo_waktu; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.saldo_waktu (id_akun, id_zona, sisa_waktu_menit) VALUES (3, 1, 275);
INSERT INTO public.saldo_waktu (id_akun, id_zona, sisa_waktu_menit) VALUES (3, 2, 130);
INSERT INTO public.saldo_waktu (id_akun, id_zona, sisa_waktu_menit) VALUES (3, 3, 0);
INSERT INTO public.saldo_waktu (id_akun, id_zona, sisa_waktu_menit) VALUES (4, 1, 150);
INSERT INTO public.saldo_waktu (id_akun, id_zona, sisa_waktu_menit) VALUES (4, 2, 10);
INSERT INTO public.saldo_waktu (id_akun, id_zona, sisa_waktu_menit) VALUES (5, 3, 350);


--
-- Data for Name: sesi_bermain; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.sesi_bermain (id_sesi, id_pc, id_akun, waktu_login, waktu_logout, durasi_terpakai_menit) VALUES (1, 'PC-01', 3, '2026-11-17 15:00:00', '2026-11-17 17:00:00', 120);
INSERT INTO public.sesi_bermain (id_sesi, id_pc, id_akun, waktu_login, waktu_logout, durasi_terpakai_menit) VALUES (2, 'NB-04', 3, '2026-11-18 11:35:00', NULL, NULL);
INSERT INTO public.sesi_bermain (id_sesi, id_pc, id_akun, waktu_login, waktu_logout, durasi_terpakai_menit) VALUES (3, 'PC-02', 4, '2026-11-18 12:00:00', NULL, NULL);
INSERT INTO public.sesi_bermain (id_sesi, id_pc, id_akun, waktu_login, waktu_logout, durasi_terpakai_menit) VALUES (4, 'RY-02', 5, '2026-11-18 10:15:00', NULL, NULL);


--
-- Data for Name: transaksi; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.transaksi (id_transaksi, id_akun_pelanggan, id_akun_kasir, waktu_transaksi, total_bayar, metode_pembayaran, status_pembayaran, kode_referensi) VALUES ('NX-9938210', 3, NULL, '2026-11-18 10:24:00', 24000, 'QRIS Dinamis', 'Sukses', 'MID-QRIS-882910A');
INSERT INTO public.transaksi (id_transaksi, id_akun_pelanggan, id_akun_kasir, waktu_transaksi, total_bayar, metode_pembayaran, status_pembayaran, kode_referensi) VALUES ('NX-9934112', 3, NULL, '2026-11-15 14:10:00', 30000, 'BCA Virtual Account', 'Sukses', 'MID-VA-771293B');
INSERT INTO public.transaksi (id_transaksi, id_akun_pelanggan, id_akun_kasir, waktu_transaksi, total_bayar, metode_pembayaran, status_pembayaran, kode_referensi) VALUES ('NX-9921094', 4, 2, '2026-11-16 20:05:00', 14000, 'Tunai Kasir', 'Sukses', 'CSH-20261116-01');
INSERT INTO public.transaksi (id_transaksi, id_akun_pelanggan, id_akun_kasir, waktu_transaksi, total_bayar, metode_pembayaran, status_pembayaran, kode_referensi) VALUES ('NX-9908842', 5, NULL, '2026-11-18 12:55:00', 75000, 'Mandiri VA', 'Pending', 'MID-VA-990128C');


--
-- Data for Name: zona; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.zona (id_zona, nama_zona, spek_cpu, spek_gpu, spek_ram, spek_monitor) VALUES (1, 'Commoner', 'Intel Core i5-12400F', 'NVIDIA RTX 4060 8GB', '16GB DDR4 3200MHz', '24" 240Hz Gaming IPS');
INSERT INTO public.zona (id_zona, nama_zona, spek_cpu, spek_gpu, spek_ram, spek_monitor) VALUES (2, 'Noble', 'Intel Core i7-13700F', 'NVIDIA RTX 4070 Ti 12GB', '32GB DDR5 5600MHz', '27" 2K 240Hz Fast IPS');
INSERT INTO public.zona (id_zona, nama_zona, spek_cpu, spek_gpu, spek_ram, spek_monitor) VALUES (3, 'Royalty', 'AMD Ryzen 9 7900X', 'NVIDIA RTX 4090 24GB (Dual PC)', '64GB DDR5 6000MHz', '27" OLED 360Hz + Shure SM7B');


--
-- Name: akun_id_akun_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.akun_id_akun_seq', 5, true);


--
-- Name: detail_transaksi_id_detail_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.detail_transaksi_id_detail_seq', 4, true);


--
-- Name: hak_akses_id_akses_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.hak_akses_id_akses_seq', 3, true);


--
-- Name: paket_billing_id_paket_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.paket_billing_id_paket_seq', 7, true);


--
-- Name: sesi_bermain_id_sesi_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sesi_bermain_id_sesi_seq', 4, true);


--
-- Name: zona_id_zona_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.zona_id_zona_seq', 3, true);


--
-- Name: akun pk_akun; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.akun
    ADD CONSTRAINT pk_akun PRIMARY KEY (id_akun);


--
-- Name: detail_transaksi pk_detail; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detail_transaksi
    ADD CONSTRAINT pk_detail PRIMARY KEY (id_detail);


--
-- Name: hak_akses pk_hak_akses; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hak_akses
    ADD CONSTRAINT pk_hak_akses PRIMARY KEY (id_akses);


--
-- Name: paket_billing pk_paket; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paket_billing
    ADD CONSTRAINT pk_paket PRIMARY KEY (id_paket);


--
-- Name: pc pk_pc; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pc
    ADD CONSTRAINT pk_pc PRIMARY KEY (id_pc);


--
-- Name: saldo_waktu pk_saldo_waktu; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saldo_waktu
    ADD CONSTRAINT pk_saldo_waktu PRIMARY KEY (id_akun, id_zona);


--
-- Name: sesi_bermain pk_sesi; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sesi_bermain
    ADD CONSTRAINT pk_sesi PRIMARY KEY (id_sesi);


--
-- Name: transaksi pk_transaksi; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaksi
    ADD CONSTRAINT pk_transaksi PRIMARY KEY (id_transaksi);


--
-- Name: zona pk_zona; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zona
    ADD CONSTRAINT pk_zona PRIMARY KEY (id_zona);


--
-- Name: akun fk_akun_akses; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.akun
    ADD CONSTRAINT fk_akun_akses FOREIGN KEY (id_akses) REFERENCES public.hak_akses(id_akses) ON DELETE RESTRICT;


--
-- Name: detail_transaksi fk_detail_transaksi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detail_transaksi
    ADD CONSTRAINT fk_detail_transaksi FOREIGN KEY (id_transaksi) REFERENCES public.transaksi(id_transaksi) ON DELETE CASCADE;


--
-- Name: detail_transaksi fk_detail_zona; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detail_transaksi
    ADD CONSTRAINT fk_detail_zona FOREIGN KEY (id_zona) REFERENCES public.zona(id_zona) ON DELETE RESTRICT;


--
-- Name: paket_billing fk_paket_zona; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paket_billing
    ADD CONSTRAINT fk_paket_zona FOREIGN KEY (id_zona) REFERENCES public.zona(id_zona) ON DELETE CASCADE;


--
-- Name: pc fk_pc_zona; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pc
    ADD CONSTRAINT fk_pc_zona FOREIGN KEY (id_zona) REFERENCES public.zona(id_zona) ON DELETE RESTRICT;


--
-- Name: saldo_waktu fk_saldo_akun; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saldo_waktu
    ADD CONSTRAINT fk_saldo_akun FOREIGN KEY (id_akun) REFERENCES public.akun(id_akun) ON DELETE CASCADE;


--
-- Name: saldo_waktu fk_saldo_zona; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saldo_waktu
    ADD CONSTRAINT fk_saldo_zona FOREIGN KEY (id_zona) REFERENCES public.zona(id_zona) ON DELETE CASCADE;


--
-- Name: sesi_bermain fk_sesi_akun; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sesi_bermain
    ADD CONSTRAINT fk_sesi_akun FOREIGN KEY (id_akun) REFERENCES public.akun(id_akun) ON DELETE CASCADE;


--
-- Name: sesi_bermain fk_sesi_pc; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sesi_bermain
    ADD CONSTRAINT fk_sesi_pc FOREIGN KEY (id_pc) REFERENCES public.pc(id_pc) ON DELETE RESTRICT;


--
-- Name: transaksi fk_transaksi_kasir; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaksi
    ADD CONSTRAINT fk_transaksi_kasir FOREIGN KEY (id_akun_kasir) REFERENCES public.akun(id_akun) ON DELETE SET NULL;


--
-- Name: transaksi fk_transaksi_pelanggan; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaksi
    ADD CONSTRAINT fk_transaksi_pelanggan FOREIGN KEY (id_akun_pelanggan) REFERENCES public.akun(id_akun) ON DELETE RESTRICT;


--
-- PostgreSQL database dump complete
--

\unrestrict nA9KWtAY2dBVygkdIGYI7ZuIfLGhKPJmkANTcFnPe1E5RB6LmWap81GWrSz6fkE

