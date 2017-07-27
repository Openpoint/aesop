<?php
/*
Copyright 2017 Michael Jonker (http://openpoint.ie)

This file is part of Aesop.

Aesop is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Aesop is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Aesop.  If not, see <http://www.gnu.org/licenses/>.
*/


$sql="
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


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: chapter; Type: TABLE; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

CREATE TABLE chapter (
    chid integer NOT NULL,
    sid smallint,
    title text,
    c_order smallint,
    subtitle text DEFAULT 'Chapter subtitle'::text,
    mentitle text DEFAULT 'Menu Title'::text
);


ALTER TABLE public.chapter OWNER TO ".$_POST['dbuser'].";

--
-- Name: chapter_chid_seq; Type: SEQUENCE; Schema: public; Owner: ".$_POST['dbuser']."
--

CREATE SEQUENCE chapter_chid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.chapter_chid_seq OWNER TO ".$_POST['dbuser'].";

--
-- Name: chapter_chid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER SEQUENCE chapter_chid_seq OWNED BY chapter.chid;


--
-- Name: page; Type: TABLE; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

CREATE TABLE page (
    pid integer NOT NULL,
    chid smallint,
    sid smallint,
    title text,
    text text DEFAULT 'Page text'::text,
    p_order smallint,
    menushow boolean DEFAULT true
);


ALTER TABLE public.page OWNER TO ".$_POST['dbuser'].";

--
-- Name: page_pid_seq; Type: SEQUENCE; Schema: public; Owner: ".$_POST['dbuser']."
--

CREATE SEQUENCE page_pid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.page_pid_seq OWNER TO ".$_POST['dbuser'].";

--
-- Name: page_pid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER SEQUENCE page_pid_seq OWNED BY page.pid;


--
-- Name: queue; Type: TABLE; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

CREATE TABLE queue (
    qid integer NOT NULL,
    pid integer,
    sid integer,
    chid integer,
    command text,
    \"time\" timestamp without time zone,
    prid integer,
    seen integer[] DEFAULT '{}'::integer[],
    status character varying(10) DEFAULT 'queued'::character varying,
    message text[] DEFAULT '{}'::text[],
    type character varying(10),
    corder smallint,
    porder smallint,
    title text
);


ALTER TABLE public.queue OWNER TO ".$_POST['dbuser'].";

--
-- Name: queue_porder_seq; Type: SEQUENCE; Schema: public; Owner: ".$_POST['dbuser']."
--

CREATE SEQUENCE queue_porder_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.queue_porder_seq OWNER TO ".$_POST['dbuser'].";

--
-- Name: queue_porder_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER SEQUENCE queue_porder_seq OWNED BY queue.porder;


--
-- Name: queue_qid_seq; Type: SEQUENCE; Schema: public; Owner: ".$_POST['dbuser']."
--

CREATE SEQUENCE queue_qid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.queue_qid_seq OWNER TO ".$_POST['dbuser'].";

--
-- Name: queue_qid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER SEQUENCE queue_qid_seq OWNED BY queue.qid;


--
-- Name: resource; Type: TABLE; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

CREATE TABLE resource (
    rid integer NOT NULL,
    type text,
    location character varying(256),
    pid smallint,
    sid smallint,
    chid smallint,
    v_mp4 character varying(256),
    v_ogv character varying(256),
    v_webm character varying(256),
    bvmute boolean DEFAULT true,
    a_mp3 character varying(256),
    a_ogg character varying(256),
    astop boolean DEFAULT false
);


ALTER TABLE public.resource OWNER TO ".$_POST['dbuser'].";

--
-- Name: resource_rid_seq; Type: SEQUENCE; Schema: public; Owner: ".$_POST['dbuser']."
--

CREATE SEQUENCE resource_rid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.resource_rid_seq OWNER TO ".$_POST['dbuser'].";

--
-- Name: resource_rid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER SEQUENCE resource_rid_seq OWNED BY resource.rid;


--
-- Name: settings; Type: TABLE; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

CREATE TABLE settings (
    pname text
);


ALTER TABLE public.settings OWNER TO ".$_POST['dbuser'].";

--
-- Name: story; Type: TABLE; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

CREATE TABLE story (
    sid integer NOT NULL,
    title character varying(256),
    text text DEFAULT 'Story summary'::text,
    location text,
	owner integer
);


ALTER TABLE public.story OWNER TO ".$_POST['dbuser'].";

--
-- Name: story_sid_seq; Type: SEQUENCE; Schema: public; Owner: ".$_POST['dbuser']."
--

CREATE SEQUENCE story_sid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.story_sid_seq OWNER TO ".$_POST['dbuser'].";

--
-- Name: story_sid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER SEQUENCE story_sid_seq OWNED BY story.sid;


--
-- Name: users; Type: TABLE; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

CREATE TABLE users (
    id integer NOT NULL,
    username text NOT NULL,
    hash text NOT NULL,
    salt text NOT NULL,
    authtoken character varying(50),
    email text NOT NULL,
    role character varying(10),
    verified boolean DEFAULT false
);


ALTER TABLE public.users OWNER TO ".$_POST['dbuser'].";

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: ".$_POST['dbuser']."
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO ".$_POST['dbuser'].";

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: chid; Type: DEFAULT; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER TABLE ONLY chapter ALTER COLUMN chid SET DEFAULT nextval('chapter_chid_seq'::regclass);


--
-- Name: pid; Type: DEFAULT; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER TABLE ONLY page ALTER COLUMN pid SET DEFAULT nextval('page_pid_seq'::regclass);


--
-- Name: qid; Type: DEFAULT; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER TABLE ONLY queue ALTER COLUMN qid SET DEFAULT nextval('queue_qid_seq'::regclass);


--
-- Name: rid; Type: DEFAULT; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER TABLE ONLY resource ALTER COLUMN rid SET DEFAULT nextval('resource_rid_seq'::regclass);


--
-- Name: sid; Type: DEFAULT; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER TABLE ONLY story ALTER COLUMN sid SET DEFAULT nextval('story_sid_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: ".$_POST['dbuser']."
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: chapter_pkey; Type: CONSTRAINT; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

ALTER TABLE ONLY chapter
    ADD CONSTRAINT chapter_pkey PRIMARY KEY (chid);


--
-- Name: page_pkey; Type: CONSTRAINT; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

ALTER TABLE ONLY page
    ADD CONSTRAINT page_pkey PRIMARY KEY (pid);


--
-- Name: queue_pkey; Type: CONSTRAINT; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

ALTER TABLE ONLY queue
    ADD CONSTRAINT queue_pkey PRIMARY KEY (qid);


--
-- Name: resource_pkey; Type: CONSTRAINT; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

ALTER TABLE ONLY resource
    ADD CONSTRAINT resource_pkey PRIMARY KEY (rid);


--
-- Name: story_pkey; Type: CONSTRAINT; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

ALTER TABLE ONLY story
    ADD CONSTRAINT story_pkey PRIMARY KEY (sid);


--
-- Name: story_title_key; Type: CONSTRAINT; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

ALTER TABLE ONLY story
    ADD CONSTRAINT story_title_key UNIQUE (title);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: ".$_POST['dbuser']."; Tablespace:
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


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
"
?>
