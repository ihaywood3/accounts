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
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA public;


--
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: chart; Type: TABLE; Schema: public; Owner: ian; Tablespace: 
--

CREATE TABLE chart (
    id integer NOT NULL,
    name text
);


ALTER TABLE public.chart OWNER TO ian;

--
-- Name: split; Type: TABLE; Schema: public; Owner: ian; Tablespace: 
--

CREATE TABLE split (
    fk_txn integer,
    fk_chart integer,
    amount money
);


ALTER TABLE public.split OWNER TO ian;

--
-- Name: txn; Type: TABLE; Schema: public; Owner: ian; Tablespace: 
--

CREATE TABLE txn (
    id integer NOT NULL,
    comment text,
    pdf bytea,
    entered date DEFAULT now() NOT NULL
);


ALTER TABLE public.txn OWNER TO ian;

--
-- Name: vwaccounts; Type: VIEW; Schema: public; Owner: ian
--

CREATE VIEW vwaccounts AS
 SELECT split.fk_txn,
    split.fk_chart,
    chart.name,
    txn.comment,
    txn.entered,
    split.amount
   FROM chart,
    split,
    txn
  WHERE ((split.fk_txn = txn.id) AND (split.fk_chart = chart.id));


ALTER TABLE public.vwaccounts OWNER TO ian;

--
-- Data for Name: chart; Type: TABLE DATA; Schema: public; Owner: ian
--

COPY chart (id, name) FROM stdin;
\.


--
-- Data for Name: split; Type: TABLE DATA; Schema: public; Owner: ian
--

COPY split (fk_txn, fk_chart, amount) FROM stdin;
\.


--
-- Data for Name: txn; Type: TABLE DATA; Schema: public; Owner: ian
--

COPY txn (id, comment, pdf, entered) FROM stdin;
\.


--
-- Name: chart_pkey; Type: CONSTRAINT; Schema: public; Owner: ian; Tablespace: 
--

ALTER TABLE ONLY chart
    ADD CONSTRAINT chart_pkey PRIMARY KEY (id);


--
-- Name: txn_pkey; Type: CONSTRAINT; Schema: public; Owner: ian; Tablespace: 
--

ALTER TABLE ONLY txn
    ADD CONSTRAINT txn_pkey PRIMARY KEY (id);


--
-- Name: split_fk_chart_fkey; Type: FK CONSTRAINT; Schema: public; Owner: ian
--

ALTER TABLE ONLY split
    ADD CONSTRAINT split_fk_chart_fkey FOREIGN KEY (fk_chart) REFERENCES chart(id);


--
-- Name: split_fk_txn_fkey; Type: FK CONSTRAINT; Schema: public; Owner: ian
--

ALTER TABLE ONLY split
    ADD CONSTRAINT split_fk_txn_fkey FOREIGN KEY (fk_txn) REFERENCES txn(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

