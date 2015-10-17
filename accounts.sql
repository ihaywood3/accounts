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

--
-- Name: total_acct(text, date, date); Type: FUNCTION; Schema: public; Owner: ian
--

CREATE FUNCTION total_acct(text, date, date) RETURNS money
    LANGUAGE sql
    AS $_$
select sum(amount) from chart,txn,split where (chart.name = $1 or chart.name like $1 || '/%') and txn.entered >= $2 and txn.entered <= $3 and split.fk_chart = chart.id and split.fk_txn = txn.id 
$_$;


ALTER FUNCTION public.total_acct(text, date, date) OWNER TO ian;

--
-- Name: chart_id; Type: SEQUENCE; Schema: public; Owner: ian
--

CREATE SEQUENCE chart_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.chart_id OWNER TO ian;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: chart; Type: TABLE; Schema: public; Owner: ian; Tablespace: 
--

CREATE TABLE chart (
    id integer DEFAULT nextval('chart_id'::regclass) NOT NULL,
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
-- Name: statement; Type: TABLE; Schema: public; Owner: ian; Tablespace: 
--

CREATE TABLE statement (
    id integer NOT NULL,
    comment text,
    date date,
    fk_chart integer,
    amount money,
    junk text
);


ALTER TABLE public.statement OWNER TO ian;

--
-- Name: statement_id_seq; Type: SEQUENCE; Schema: public; Owner: ian
--

CREATE SEQUENCE statement_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.statement_id_seq OWNER TO ian;

--
-- Name: statement_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ian
--

ALTER SEQUENCE statement_id_seq OWNED BY statement.id;


--
-- Name: txn_id; Type: SEQUENCE; Schema: public; Owner: ian
--

CREATE SEQUENCE txn_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.txn_id OWNER TO ian;

--
-- Name: txn; Type: TABLE; Schema: public; Owner: ian; Tablespace: 
--

CREATE TABLE txn (
    id integer DEFAULT nextval('txn_id'::regclass) NOT NULL,
    comment text,
    pdf bytea,
    entered date DEFAULT now() NOT NULL
);


ALTER TABLE public.txn OWNER TO ian;

--
-- Name: id; Type: DEFAULT; Schema: public; Owner: ian
--

ALTER TABLE ONLY statement ALTER COLUMN id SET DEFAULT nextval('statement_id_seq'::regclass);


--
-- Name: chart_pkey; Type: CONSTRAINT; Schema: public; Owner: ian; Tablespace: 
--

ALTER TABLE ONLY chart
    ADD CONSTRAINT chart_pkey PRIMARY KEY (id);


--
-- Name: statement_pkey; Type: CONSTRAINT; Schema: public; Owner: ian; Tablespace: 
--

ALTER TABLE ONLY statement
    ADD CONSTRAINT statement_pkey PRIMARY KEY (id);


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
-- Name: statement_fk_chart_fkey; Type: FK CONSTRAINT; Schema: public; Owner: ian
--

ALTER TABLE ONLY statement
    ADD CONSTRAINT statement_fk_chart_fkey FOREIGN KEY (fk_chart) REFERENCES chart(id);


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

