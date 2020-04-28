--
-- PostgreSQL database dump
--

-- Dumped from database version 11.7
-- Dumped by pg_dump version 12.2

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: admin; Type: TABLE DATA; Schema: public; Owner: main
--

COPY public.admin (id, username, roles, password) FROM stdin;
12	admin	["ROLE_ADMIN"]	$argon2id$v=19$m=65536,t=4,p=1$VL/8ZWEQind/pYjnq5woaQ$yGcPaoS23IVaLTEsUkVZLAi6h1DoE8nO75gr84isPB0
\.


--
-- Data for Name: conference; Type: TABLE DATA; Schema: public; Owner: main
--

COPY public.conference (id, city, year, is_international, slug) FROM stdin;
50	Amsterdam	2019	t	amsterdam-2019
51	Paris	2020	f	paris-2020
\.


--
-- Data for Name: comment; Type: TABLE DATA; Schema: public; Owner: main
--

COPY public.comment (id, conference_id, author, text, email, created_at, photo_filename, state) FROM stdin;
66	50	Lucas	I think this one is going to be moderated.	lucas@example.com	2020-04-26 09:42:15	\N	submitted
67	50	Fabien	This was a great conference.	fabien@example.com	2020-04-26 09:42:15	\N	published
69	50	asdasd	123123213	asd@asd.sa	2020-04-26 09:50:54	\N	submitted
70	50	asdasd	123123213	asd@asd.sa	2020-04-26 09:51:21	\N	submitted
71	50	asdasd	123123213	asd@asd.sa	2020-04-26 09:51:26	\N	published
\.


--
-- Data for Name: migration_versions; Type: TABLE DATA; Schema: public; Owner: main
--

COPY public.migration_versions (version, executed_at) FROM stdin;
20200421053557	2020-04-21 05:36:15
20200422095340	2020-04-22 09:59:50
20200422100152	2020-04-22 10:01:57
20200423073746	2020-04-23 07:37:51
20200423082234	2020-04-23 08:22:40
20200426060724	2020-04-26 06:07:36
20200426061059	2020-04-26 06:11:13
20200426084425	2020-04-26 08:45:07
\.


--
-- Name: admin_id_seq; Type: SEQUENCE SET; Schema: public; Owner: main
--

SELECT pg_catalog.setval('public.admin_id_seq', 12, true);


--
-- Name: comment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: main
--

SELECT pg_catalog.setval('public.comment_id_seq', 71, true);


--
-- Name: conference_id_seq; Type: SEQUENCE SET; Schema: public; Owner: main
--

SELECT pg_catalog.setval('public.conference_id_seq', 51, true);


--
-- PostgreSQL database dump complete
--

