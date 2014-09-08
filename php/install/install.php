<?php
include('../set.php');
escape($data);
$user="website";

$addhash="
--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: ".$p_hash."; Type: SCHEMA; Schema: -; Owner: ".$user."
--

CREATE SCHEMA ".$p_hash.";


ALTER SCHEMA ".$p_hash." OWNER TO ".$user.";

SET search_path = ".$p_hash.", pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: answers; Type: TABLE; Schema: ".$p_hash."; Owner: ".$user."; Tablespace: 
--

CREATE TABLE answers (
    id bigint,
    yes boolean,
    meh boolean,
    no boolean,
    ip inet,
    city text,
    country text,
    \"time\" time without time zone
);


ALTER TABLE ".$p_hash.".answers OWNER TO ".$user.";

--
-- Name: questions; Type: TABLE; Schema: ".$p_hash."; Owner: ".$user."; Tablespace: 
--

CREATE TABLE questions (
    id integer NOT NULL,
    text text,
    official boolean,
    popularity integer
);


ALTER TABLE ".$p_hash.".questions OWNER TO ".$user.";

--
-- Name: questions_id_seq; Type: SEQUENCE; Schema: ".$p_hash."; Owner: ".$user."
--

CREATE SEQUENCE questions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE ".$p_hash.".questions_id_seq OWNER TO ".$user.";

--
-- Name: questions_id_seq; Type: SEQUENCE OWNED BY; Schema: ".$p_hash."; Owner: ".$user."
--

ALTER SEQUENCE questions_id_seq OWNED BY questions.id;


--
-- Name: id; Type: DEFAULT; Schema: ".$p_hash."; Owner: ".$user."
--

ALTER TABLE ONLY questions ALTER COLUMN id SET DEFAULT nextval('questions_id_seq'::regclass);


--
-- Name: questions_pkey; Type: CONSTRAINT; Schema: ".$p_hash."; Owner: ".$user."; Tablespace: 
--

ALTER TABLE ONLY questions
    ADD CONSTRAINT questions_pkey PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--
";
$public="
--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: settings; Type: TABLE; Schema: public; Owner: ".$user."; Tablespace: 
--

CREATE TABLE settings (
    smtp text[]
);


ALTER TABLE public.settings OWNER TO ".$user.";

--
-- Name: users; Type: TABLE; Schema: public; Owner: ".$user."; Tablespace: 
--

CREATE TABLE users (
    id integer NOT NULL,
    username text NOT NULL,
    hash text NOT NULL,
    salt text NOT NULL,
    authtoken character varying(50),
    email text NOT NULL
);


ALTER TABLE public.users OWNER TO ".$user.";

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: ".$user."
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO ".$user.";

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ".$user."
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: ".$user."
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: ".$user."; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: public; Type: ACL; Schema: -; Owner: ".$user."
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM ".$user.";
GRANT ALL ON SCHEMA public TO ".$user.";
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--
";
function addhash(){
	global $dbh,$addhash;
	echo($addhash);
	$result = pg_query($dbh, $addhash);
	if (!$result) {
		pg_close($dbh);
		die("Error in SQL query: " . pg_last_error());
	}else{
		echo('____success____');
		pg_close($dbh);
	}
}
if ($data->method == 'newhash'){
	addhash();
}
?>
