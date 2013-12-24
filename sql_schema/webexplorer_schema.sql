--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: webexplorer; Type: DATABASE; Schema: -; Owner: -
--

CREATE DATABASE "webexplorer" WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8';


\connect "webexplorer"

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: array_agg(anyelement); Type: AGGREGATE; Schema: public; Owner: -
--

CREATE AGGREGATE array_agg(anyelement) (
    SFUNC = array_append,
    STYPE = anyarray,
    INITCOND = '{}'
);


--
-- Name: textcat_all(text); Type: AGGREGATE; Schema: public; Owner: -
--

CREATE AGGREGATE textcat_all(text) (
    SFUNC = textcat,
    STYPE = text,
    INITCOND = ''
);


SET default_with_oids = false;

--
-- Name: webpages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE webpages (
    id integer NOT NULL,
    name character varying(50) NOT NULL,
    html text,
    css text,
    js text,
    user_id character varying(50),
    modified timestamp without time zone
);


--
-- Name: htmlpages_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE htmlpages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: htmlpages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE htmlpages_id_seq OWNED BY webpages.id;


--
-- Name: logs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE logs (
    id bigint NOT NULL,
    activity character varying(255) NOT NULL,
    question_id integer,
    query text,
    error text,
    "user" character varying(255),
    created timestamp without time zone,
    result boolean DEFAULT false,
    ip character varying(15)
);


--
-- Name: log_activity_group; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW log_activity_group AS
SELECT logs.activity, logs."user", logs.result, count(*) AS count FROM logs GROUP BY logs."user", logs.activity, logs.result ORDER BY logs."user";


--
-- Name: log_activity_result; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW log_activity_result AS
SELECT a1.activity, a1."user", max(a1.count) AS "false", max(a2.count) AS "true" FROM (log_activity_group a1 LEFT JOIN log_activity_group a2 ON ((((((a1.activity)::text = (a2.activity)::text) AND ((a1."user")::text = (a2."user")::text)) AND (a1.result <> true)) AND (a2.result <> false)))) GROUP BY a1.activity, a1."user" ORDER BY a1.activity, a1."user";


--
-- Name: logs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE logs_id_seq OWNED BY logs.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE users (
    matricule character(8) NOT NULL,
    username character varying(100),
    password character(40),
    last_name character varying(255),
    first_name character varying(255),
    email character varying(255),
    gender character varying(1),
    modified timestamp without time zone
);


--
-- Name: webpage_snapshots; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE webpage_snapshots (
    id integer NOT NULL,
    webpage_id integer NOT NULL,
    name character varying(255),
    html text,
    css text,
    js text,
    created timestamp without time zone
);


--
-- Name: saves_by_webpage; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW saves_by_webpage AS
SELECT ws.webpage_id, u.first_name, u.last_name, count(*) AS count FROM ((webpage_snapshots ws JOIN webpages w ON ((w.id = ws.webpage_id))) JOIN users u ON ((u.matricule = (w.user_id)::bpchar))) GROUP BY ws.webpage_id, u.first_name, u.last_name ORDER BY count(*) DESC;


--
-- Name: webpage_tps; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE webpage_tps (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    user_id character(8) NOT NULL,
    webpage_snapshot_id integer NOT NULL,
    modified timestamp without time zone,
    point integer,
    evaluator_id character(8),
    comment text
);


--
-- Name: tps_md5; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW tps_md5 AS
SELECT u.matricule, u.first_name, u.last_name, w.html, w.css, w.js, md5(regexp_replace(w.html, '\s'::text, ''::text, 'g'::text)) AS md5_html, md5(regexp_replace(w.css, '\s'::text, ''::text, 'g'::text)) AS md5_css, md5(regexp_replace(w.js, '\s'::text, ''::text, 'g'::text)) AS md5_js, w.created, tp.id, tp.name, tp.point, tp.evaluator_id FROM webpage_tps tp, webpage_snapshots w, users u WHERE ((u.matricule = tp.user_id) AND (w.id = tp.webpage_snapshot_id)) ORDER BY w.created;


--
-- Name: webpage_snapshots_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE webpage_snapshots_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: webpage_snapshots_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE webpage_snapshots_id_seq OWNED BY webpage_snapshots.id;


--
-- Name: webpages_tp_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE webpages_tp_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: webpages_tp_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE webpages_tp_id_seq OWNED BY webpage_tps.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY logs ALTER COLUMN id SET DEFAULT nextval('logs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY webpage_snapshots ALTER COLUMN id SET DEFAULT nextval('webpage_snapshots_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY webpage_tps ALTER COLUMN id SET DEFAULT nextval('webpages_tp_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY webpages ALTER COLUMN id SET DEFAULT nextval('htmlpages_id_seq'::regclass);


--
-- Name: htmlpages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY webpages
    ADD CONSTRAINT htmlpages_pkey PRIMARY KEY (id);


--
-- Name: logs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY logs
    ADD CONSTRAINT logs_pkey PRIMARY KEY (id);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (matricule);


--
-- Name: users_username_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- Name: webpage_snapshots_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY webpage_snapshots
    ADD CONSTRAINT webpage_snapshots_pkey PRIMARY KEY (id);


--
-- Name: webpage_tps_name_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY webpage_tps
    ADD CONSTRAINT webpage_tps_name_key UNIQUE (name, user_id);


--
-- Name: webpages_tp_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY webpage_tps
    ADD CONSTRAINT webpages_tp_pkey PRIMARY KEY (id);


--
-- Name: logs_created_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX logs_created_idx ON logs USING btree (created);


--
-- Name: logs_user_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX logs_user_idx ON logs USING btree ("user");


--
-- Name: name_unique_to_user; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX name_unique_to_user ON webpages USING btree (name, user_id);


--
-- PostgreSQL database dump complete
--

