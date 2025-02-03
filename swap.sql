--
-- PostgreSQL database dump
--

-- Dumped from database version 17.0
-- Dumped by pg_dump version 17.0

-- Started on 2025-02-03 12:24:44

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
-- TOC entry 218 (class 1259 OID 33426)
-- Name: agent; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.agent (
    id_agent uuid DEFAULT gen_random_uuid() NOT NULL,
    pseudo character varying(100) NOT NULL,
    phone_number character varying(20) NOT NULL,
    rating_global double precision,
    id_user uuid NOT NULL
);


ALTER TABLE public.agent OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 33496)
-- Name: agent_skill; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.agent_skill (
    id_agent uuid NOT NULL,
    id_skill uuid NOT NULL
);


ALTER TABLE public.agent_skill OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 33454)
-- Name: conversation; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.conversation (
    id_conversation uuid DEFAULT gen_random_uuid() NOT NULL,
    started_at timestamp without time zone NOT NULL,
    id_customer uuid NOT NULL,
    id_agent uuid NOT NULL
);


ALTER TABLE public.conversation OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 33439)
-- Name: customer; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customer (
    id_customer uuid DEFAULT gen_random_uuid() NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    address character varying(255) NOT NULL,
    id_user uuid NOT NULL
);


ALTER TABLE public.customer OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 33470)
-- Name: message; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.message (
    id_message uuid DEFAULT gen_random_uuid() NOT NULL,
    content text NOT NULL,
    sent_at timestamp without time zone NOT NULL,
    id_conversation uuid NOT NULL,
    id_user uuid NOT NULL
);


ALTER TABLE public.message OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 33556)
-- Name: review; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.review (
    id_review uuid DEFAULT gen_random_uuid() NOT NULL,
    rating integer NOT NULL,
    comment text,
    created_at timestamp without time zone NOT NULL,
    id_agent uuid NOT NULL,
    id_customer uuid NOT NULL,
    CONSTRAINT review_rating_check CHECK (((rating >= 1) AND (rating <= 5)))
);


ALTER TABLE public.review OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 33488)
-- Name: skill; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.skill (
    id_skill uuid DEFAULT gen_random_uuid() NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255)
);


ALTER TABLE public.skill OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 33511)
-- Name: tag; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tag (
    id_tag uuid DEFAULT gen_random_uuid() NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.tag OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 33517)
-- Name: task; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.task (
    id_task uuid DEFAULT gen_random_uuid() NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    status character varying(50) NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


ALTER TABLE public.task OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 33540)
-- Name: task_proposal; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.task_proposal (
    id_task_proposal uuid DEFAULT gen_random_uuid() NOT NULL,
    proposed_price double precision NOT NULL,
    status character varying(50) NOT NULL,
    created_at timestamp without time zone NOT NULL,
    id_agent uuid NOT NULL,
    id_task uuid NOT NULL
);


ALTER TABLE public.task_proposal OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 33525)
-- Name: task_tag; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.task_tag (
    id_task uuid NOT NULL,
    id_tag uuid NOT NULL
);


ALTER TABLE public.task_tag OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 33416)
-- Name: user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."user" (
    id_user uuid DEFAULT gen_random_uuid() NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    roles text NOT NULL
);


ALTER TABLE public."user" OWNER TO postgres;

--
-- TOC entry 4939 (class 0 OID 33426)
-- Dependencies: 218
-- Data for Name: agent; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.agent (id_agent, pseudo, phone_number, rating_global, id_user) FROM stdin;
\.


--
-- TOC entry 4944 (class 0 OID 33496)
-- Dependencies: 223
-- Data for Name: agent_skill; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.agent_skill (id_agent, id_skill) FROM stdin;
\.


--
-- TOC entry 4941 (class 0 OID 33454)
-- Dependencies: 220
-- Data for Name: conversation; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.conversation (id_conversation, started_at, id_customer, id_agent) FROM stdin;
\.


--
-- TOC entry 4940 (class 0 OID 33439)
-- Dependencies: 219
-- Data for Name: customer; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.customer (id_customer, first_name, last_name, address, id_user) FROM stdin;
\.


--
-- TOC entry 4942 (class 0 OID 33470)
-- Dependencies: 221
-- Data for Name: message; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.message (id_message, content, sent_at, id_conversation, id_user) FROM stdin;
\.


--
-- TOC entry 4949 (class 0 OID 33556)
-- Dependencies: 228
-- Data for Name: review; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.review (id_review, rating, comment, created_at, id_agent, id_customer) FROM stdin;
\.


--
-- TOC entry 4943 (class 0 OID 33488)
-- Dependencies: 222
-- Data for Name: skill; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.skill (id_skill, name, description) FROM stdin;
\.


--
-- TOC entry 4945 (class 0 OID 33511)
-- Dependencies: 224
-- Data for Name: tag; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tag (id_tag, name) FROM stdin;
\.


--
-- TOC entry 4946 (class 0 OID 33517)
-- Dependencies: 225
-- Data for Name: task; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.task (id_task, title, description, status, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 4948 (class 0 OID 33540)
-- Dependencies: 227
-- Data for Name: task_proposal; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.task_proposal (id_task_proposal, proposed_price, status, created_at, id_agent, id_task) FROM stdin;
\.


--
-- TOC entry 4947 (class 0 OID 33525)
-- Dependencies: 226
-- Data for Name: task_tag; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.task_tag (id_task, id_tag) FROM stdin;
\.


--
-- TOC entry 4938 (class 0 OID 33416)
-- Dependencies: 217
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public."user" (id_user, email, password, roles) FROM stdin;
\.


--
-- TOC entry 4754 (class 2606 OID 33433)
-- Name: agent agent_id_user_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agent
    ADD CONSTRAINT agent_id_user_key UNIQUE (id_user);


--
-- TOC entry 4756 (class 2606 OID 33431)
-- Name: agent agent_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agent
    ADD CONSTRAINT agent_pkey PRIMARY KEY (id_agent);


--
-- TOC entry 4768 (class 2606 OID 33500)
-- Name: agent_skill agent_skill_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agent_skill
    ADD CONSTRAINT agent_skill_pkey PRIMARY KEY (id_agent, id_skill);


--
-- TOC entry 4762 (class 2606 OID 33459)
-- Name: conversation conversation_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversation
    ADD CONSTRAINT conversation_pkey PRIMARY KEY (id_conversation);


--
-- TOC entry 4758 (class 2606 OID 33448)
-- Name: customer customer_id_user_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer
    ADD CONSTRAINT customer_id_user_key UNIQUE (id_user);


--
-- TOC entry 4760 (class 2606 OID 33446)
-- Name: customer customer_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer
    ADD CONSTRAINT customer_pkey PRIMARY KEY (id_customer);


--
-- TOC entry 4764 (class 2606 OID 33477)
-- Name: message message_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message
    ADD CONSTRAINT message_pkey PRIMARY KEY (id_message);


--
-- TOC entry 4778 (class 2606 OID 33564)
-- Name: review review_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_pkey PRIMARY KEY (id_review);


--
-- TOC entry 4766 (class 2606 OID 33495)
-- Name: skill skill_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.skill
    ADD CONSTRAINT skill_pkey PRIMARY KEY (id_skill);


--
-- TOC entry 4770 (class 2606 OID 33516)
-- Name: tag tag_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tag
    ADD CONSTRAINT tag_pkey PRIMARY KEY (id_tag);


--
-- TOC entry 4772 (class 2606 OID 33524)
-- Name: task task_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task
    ADD CONSTRAINT task_pkey PRIMARY KEY (id_task);


--
-- TOC entry 4776 (class 2606 OID 33545)
-- Name: task_proposal task_proposal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_proposal
    ADD CONSTRAINT task_proposal_pkey PRIMARY KEY (id_task_proposal);


--
-- TOC entry 4774 (class 2606 OID 33529)
-- Name: task_tag task_tag_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_tag
    ADD CONSTRAINT task_tag_pkey PRIMARY KEY (id_task, id_tag);


--
-- TOC entry 4750 (class 2606 OID 33425)
-- Name: user user_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_email_key UNIQUE (email);


--
-- TOC entry 4752 (class 2606 OID 33423)
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id_user);


--
-- TOC entry 4779 (class 2606 OID 33434)
-- Name: agent agent_id_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agent
    ADD CONSTRAINT agent_id_user_fkey FOREIGN KEY (id_user) REFERENCES public."user"(id_user) ON DELETE CASCADE;


--
-- TOC entry 4785 (class 2606 OID 33501)
-- Name: agent_skill agent_skill_id_agent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agent_skill
    ADD CONSTRAINT agent_skill_id_agent_fkey FOREIGN KEY (id_agent) REFERENCES public.agent(id_agent) ON DELETE CASCADE;


--
-- TOC entry 4786 (class 2606 OID 33506)
-- Name: agent_skill agent_skill_id_skill_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agent_skill
    ADD CONSTRAINT agent_skill_id_skill_fkey FOREIGN KEY (id_skill) REFERENCES public.skill(id_skill) ON DELETE CASCADE;


--
-- TOC entry 4781 (class 2606 OID 33465)
-- Name: conversation conversation_id_agent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversation
    ADD CONSTRAINT conversation_id_agent_fkey FOREIGN KEY (id_agent) REFERENCES public.agent(id_agent) ON DELETE CASCADE;


--
-- TOC entry 4782 (class 2606 OID 33460)
-- Name: conversation conversation_id_customer_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversation
    ADD CONSTRAINT conversation_id_customer_fkey FOREIGN KEY (id_customer) REFERENCES public.customer(id_customer) ON DELETE CASCADE;


--
-- TOC entry 4780 (class 2606 OID 33449)
-- Name: customer customer_id_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer
    ADD CONSTRAINT customer_id_user_fkey FOREIGN KEY (id_user) REFERENCES public."user"(id_user) ON DELETE CASCADE;


--
-- TOC entry 4783 (class 2606 OID 33478)
-- Name: message message_id_conversation_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message
    ADD CONSTRAINT message_id_conversation_fkey FOREIGN KEY (id_conversation) REFERENCES public.conversation(id_conversation) ON DELETE CASCADE;


--
-- TOC entry 4784 (class 2606 OID 33483)
-- Name: message message_id_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message
    ADD CONSTRAINT message_id_user_fkey FOREIGN KEY (id_user) REFERENCES public."user"(id_user) ON DELETE CASCADE;


--
-- TOC entry 4791 (class 2606 OID 33565)
-- Name: review review_id_agent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_id_agent_fkey FOREIGN KEY (id_agent) REFERENCES public.agent(id_agent) ON DELETE CASCADE;


--
-- TOC entry 4792 (class 2606 OID 33570)
-- Name: review review_id_customer_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_id_customer_fkey FOREIGN KEY (id_customer) REFERENCES public.customer(id_customer) ON DELETE CASCADE;


--
-- TOC entry 4789 (class 2606 OID 33546)
-- Name: task_proposal task_proposal_id_agent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_proposal
    ADD CONSTRAINT task_proposal_id_agent_fkey FOREIGN KEY (id_agent) REFERENCES public.agent(id_agent) ON DELETE CASCADE;


--
-- TOC entry 4790 (class 2606 OID 33551)
-- Name: task_proposal task_proposal_id_task_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_proposal
    ADD CONSTRAINT task_proposal_id_task_fkey FOREIGN KEY (id_task) REFERENCES public.task(id_task) ON DELETE CASCADE;


--
-- TOC entry 4787 (class 2606 OID 33535)
-- Name: task_tag task_tag_id_tag_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_tag
    ADD CONSTRAINT task_tag_id_tag_fkey FOREIGN KEY (id_tag) REFERENCES public.tag(id_tag) ON DELETE CASCADE;


--
-- TOC entry 4788 (class 2606 OID 33530)
-- Name: task_tag task_tag_id_task_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_tag
    ADD CONSTRAINT task_tag_id_task_fkey FOREIGN KEY (id_task) REFERENCES public.task(id_task) ON DELETE CASCADE;


-- Completed on 2025-02-03 12:24:46

--
-- PostgreSQL database dump complete
--

