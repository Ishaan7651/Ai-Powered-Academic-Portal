-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 16, 2026 at 07:02 AM
-- Server version: 8.0.43
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `college_academic_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_assignments`
--

CREATE TABLE `ai_assignments` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `resource_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `type` enum('research','essay','project','case_study','presentation') COLLATE utf8mb4_general_ci DEFAULT 'research',
  `difficulty` enum('easy','medium','hard') COLLATE utf8mb4_general_ci DEFAULT 'medium',
  `word_count` int DEFAULT '500',
  `assignment_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_published` tinyint(1) DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `due_weeks` int DEFAULT '2'
) ;

--
-- Dumping data for table `ai_assignments`
--

INSERT INTO `ai_assignments` (`id`, `user_id`, `resource_id`, `subject_id`, `title`, `description`, `type`, `difficulty`, `word_count`, `assignment_data`, `is_published`, `published_at`, `created_at`, `updated_at`, `due_weeks`) VALUES
(1, 6, 10, NULL, 'assign', NULL, 'research', 'medium', 1000, '{\"assignment\":{\"title\":\"The Biopsychosocial Impact of Stress: From Physiological Manifestations to Trauma-Related Disorders\",\"description\":\"This research assignment requires you to delve into the intricate relationship between stress, physical health, and mental well-being, building upon the foundational concepts introduced in Module 2. You will investigate how acute and chronic stress manifest physiologically, the psychological factors that mediate stress responses, and critically analyze the diagnostic criteria and clinical implications of key Trauma and Stressor-Related Disorders as defined by the DSM-5. The assignment demands a comprehensive understanding of the mind-body connection in the context of stress, requiring you to synthesize information from the provided document with external academic research to present a nuanced and evidence-based analysis.\",\"objectives\":[\"Analyze the physiological and psychological mechanisms through which acute and chronic stress impact physical health.\",\"Differentiate between various coping mechanisms and their influence on stress outcomes, supported by empirical evidence.\",\"Investigate the diagnostic criteria, etiology, and clinical presentation of Posttraumatic Stress Disorder (PTSD), Acute Stress Disorder, and Adjustment Disorder as per the DSM-5.\",\"Evaluate the interplay between physical symptoms of stress and the development or exacerbation of mental health disorders.\",\"Synthesize research findings to discuss potential preventative strategies and interventions for managing stress and mitigating the risk of trauma-related disorders.\"],\"tasks\":[{\"task_number\":1,\"task_title\":\"The Physiological and Psychological Landscape of Stress\",\"description\":\"Drawing upon the provided document and external academic sources, explain the fundamental physiological and psychological mechanisms by which stress impacts physical health. Differentiate clearly between acute and chronic stress, providing detailed examples of their distinct effects on the body\'s systems (e.g., cardiovascular, immune, nervous). Furthermore, analyze how individual perception of stress and various coping mechanisms (both healthy and unhealthy) can significantly influence physical health outcomes. Support your analysis with relevant research findings.\",\"word_count\":350},{\"task_number\":2,\"task_title\":\"From Physical Symptoms to Mental Health Vulnerability\",\"description\":\"Elaborate on the common physical symptoms associated with stress, such as headaches, muscle pain, chest pain, fatigue, and digestive issues. For each, explain the underlying physiological processes linking stress to these manifestations. Critically discuss how the persistent experience of these physical symptoms, particularly in the context of chronic stress, can contribute to or exacerbate vulnerability to mental health challenges, serving as a bridge to the development of more severe stress-related disorders. Provide specific examples to illustrate your points.\",\"word_count\":300},{\"task_number\":3,\"task_title\":\"DSM-5 Trauma and Stressor-Related Disorders: Analysis and Interventions\",\"description\":\"Focusing on Posttraumatic Stress Disorder (PTSD), Acute Stress Disorder, and Adjustment Disorder, conduct in-depth research into their diagnostic criteria as outlined in the DSM-5. For each disorder, describe its key features, typical onset, and potential risk factors. Discuss the clinical implications of these disorders, including their impact on an individual\'s daily functioning and long-term well-being. Conclude by briefly exploring evidence-based therapeutic interventions or preventative strategies that address the complex interplay between stress, trauma, and these specific mental health conditions.\",\"word_count\":350}],\"evaluation_criteria\":[{\"criterion\":\"Content Accuracy and Depth\",\"weight\":\"40%\",\"description\":\"Accuracy of information, thoroughness of explanation, and depth of analysis regarding stress mechanisms, physical impacts, psychological factors, and DSM-5 disorder criteria. Evidence of critical thinking and synthesis of information.\"},{\"criterion\":\"Research and Referencing\",\"weight\":\"25%\",\"description\":\"Quality and relevance of external academic sources used to support arguments. Correct application of APA 7th edition referencing style for in-text citations and reference list.\"},{\"criterion\":\"Organization and Clarity\",\"weight\":\"20%\",\"description\":\"Logical structure, clear articulation of ideas, coherence between sections, and overall readability. Adherence to academic writing standards.\"},{\"criterion\":\"Adherence to Assignment Requirements\",\"weight\":\"15%\",\"description\":\"Fulfillment of all task instructions, including word count guidelines for each section, and overall assignment length. Proper formatting and submission according to guidelines.\"}],\"resources\":[\"American Psychiatric Association. (2013). Diagnostic and statistical manual of mental disorders (5th ed.).\",\"Academic databases (e.g., PubMed, PsycINFO, Google Scholar) for peer-reviewed articles on stress, health psychology, and psychopathology.\",\"University library resources on clinical psychology and health psychology textbooks.\",\"Reliable online resources from reputable psychological associations (e.g., American Psychological Association, National Institute of Mental Health).\"],\"submission_guidelines\":[\"The assignment must be submitted as a single document (e.g., .docx or .pdf) via the designated Learning Management System (LMS) portal.\",\"All sources must be cited using APA 7th edition style, including both in-text citations and a comprehensive reference list at the end of the document.\",\"The total word count for the assignment should be approximately 1000 words (excluding title page and reference list), with adherence to individual task word counts.\",\"Plagiarism will not be tolerated and will result in severe academic penalties. Ensure all work is original and properly attributed.\"],\"due_date\":\"Due in 2 weeks\",\"total_marks\":100}}', 0, NULL, '2026-03-10 20:18:00', '2026-03-10 20:18:00', 2),
(2, 6, 9, NULL, 'assign3', NULL, 'research', 'medium', 1000, '{\"assignment\":{\"title\":\"assign3\",\"description\":\"This research assignment requires an in-depth analysis and investigation into Avoidant Personality Disorder (AvPD), one of the Cluster C personality disorders characterized by anxiety, fearfulness, and excessive worry. Drawing primarily from the provided module notes, students will explore the clinical features, diagnostic criteria, differential diagnoses, and causal factors of AvPD. Furthermore, students are expected to research and discuss relevant treatment approaches, building upon the foundational understanding provided. The assignment aims to foster a comprehensive understanding of AvPD\'s complexities, its distinctions from similar conditions, and the interplay of biological and environmental factors in its development.\",\"objectives\":[\"To describe the core clinical features and diagnostic criteria of Avoidant Personality Disorder (AvPD).\",\"To critically differentiate AvPD from similar conditions such as Schizoid Personality Disorder and Social Phobia (Social Anxiety Disorder).\",\"To analyze the various causal factors contributing to AvPD, including inhibited temperament, genetic vulnerabilities, and early emotional experiences, within the framework of the diathesis-stress model.\",\"To research and evaluate common therapeutic approaches applicable to AvPD, considering its overlap with social phobia.\",\"To synthesize complex psychological information into a well-structured and coherent research paper.\"],\"tasks\":[{\"task_number\":1,\"task_title\":\"Clinical Presentation and Diagnostic Criteria of AvPD\",\"description\":\"Describe the key clinical features of Avoidant Personality Disorder, including extreme social inhibition, desire for affection coupled with fear of rejection, low self-esteem, and avoidance of novel experiences. Subsequently, detail the DSM-5 diagnostic criteria for AvPD, explaining each criterion\'s significance in defining the disorder.\",\"word_count\":250},{\"task_number\":2,\"task_title\":\"Differential Diagnosis: AvPD vs. Similar Conditions\",\"description\":\"Conduct a comparative analysis to distinguish Avoidant Personality Disorder from Schizoid Personality Disorder and Social Phobia (Social Anxiety Disorder). For each comparison, clearly articulate the similarities and, more importantly, the critical differences in terms of underlying motivations for social withdrawal, desire for relationships, emotional experience, and the pervasiveness of symptoms. Discuss the argument that AvPD may represent a more severe and chronic form of generalized social phobia.\",\"word_count\":250},{\"task_number\":3,\"task_title\":\"Etiological Factors and the Diathesis-Stress Model\",\"description\":\"Investigate the causal factors implicated in the development of AvPD. Elaborate on the concept of an \'inhibited temperament\' as an inborn biological predisposition. Discuss the role of genetic vulnerability, highlighting its partial overlap with social phobia, and identify specific heritable traits contributing to risk (e.g., fear of rejection, neuroticism). Explain how these biological vulnerabilities interact with early emotional experiences, such as emotional abuse or critical parenting, within the framework of the diathesis-stress model to shape the development of AvPD.\",\"word_count\":300},{\"task_number\":4,\"task_title\":\"Treatment Approaches for Avoidant Personality Disorder\",\"description\":\"Based on the understanding that AvPD treatment often resembles that of social phobia, research and describe specific therapeutic techniques and modalities commonly employed for these conditions. Discuss how these techniques (e.g., cognitive-behavioral therapy, social skills training, exposure therapy) address the core symptoms of AvPD, such as fear of criticism, social anxiety, and avoidance behaviors. Briefly discuss the challenges inherent in treating individuals with AvPD.\",\"word_count\":200}],\"evaluation_criteria\":[{\"criterion\":\"Content Quality and Accuracy\",\"weight\":\"40%\",\"description\":\"Demonstrates a thorough understanding of AvPD\'s clinical features, diagnostic criteria, differential diagnoses, and causal factors. Information is accurate, well-supported, and reflects critical analysis of the provided document and external research (for Task 4).\"},{\"criterion\":\"Analysis and Critical Thinking\",\"weight\":\"30%\",\"description\":\"Provides insightful comparisons between AvPD and similar conditions. Effectively explains complex concepts like the diathesis-stress model. Offers a thoughtful discussion of treatment approaches and challenges.\"},{\"criterion\":\"Structure, Organization, and Cohesion\",\"weight\":\"15%\",\"description\":\"The assignment is logically structured with clear headings and transitions. Ideas flow smoothly, and the overall argument is coherent and easy to follow.\"},{\"criterion\":\"Clarity, Language, and Referencing\",\"weight\":\"10%\",\"description\":\"Writing is clear, concise, and uses appropriate academic language. Any external sources used (especially for Task 4) are properly cited according to a recognized academic style (e.g., APA).\"},{\"criterion\":\"Adherence to Word Count and Formatting\",\"weight\":\"5%\",\"description\":\"The assignment adheres to the specified word count for each task and overall, and follows general academic formatting guidelines.\"}],\"resources\":[\"Provided \'Module 1 Notes: Cluster C Personality Disorders\' document.\",\"Diagnostic and Statistical Manual of Mental Disorders (DSM-5-TR).\",\"Recommended Clinical Psychology textbooks (e.g., Barlow, D. H., & Durand, V. M. \'Abnormal Psychology: An Integrative Approach\').\",\"Academic databases (e.g., PubMed, PsycINFO) for research on AvPD treatment approaches.\"],\"submission_guidelines\":[\"Submit your assignment as a single document (e.g., PDF or Word document).\",\"Ensure your name, student ID, and course code are clearly stated on the first page.\",\"Use a standard font (e.g., Times New Roman 12pt) with 1.5 line spacing.\",\"Cite all sources used, including the provided module notes, using APA style.\",\"Plagiarism will not be tolerated and will result in severe penalties.\"],\"due_date\":\"Due in 2 weeks\",\"total_marks\":100}}', 0, NULL, '2026-03-10 21:09:27', '2026-03-10 21:09:27', 2),
(3, 6, 9, 7, 'Assignment5', NULL, 'research', 'medium', 1000, '{\"assignment\":{\"title\":\"Assignment5\",\"description\":\"This research assignment requires an in-depth analysis of Avoidant Personality Disorder (AvPD), drawing upon the provided document content and external academic sources. Students will investigate the multifaceted nature of AvPD, including its clinical features, differential diagnoses, causal factors, and treatment approaches. The assignment aims to foster a comprehensive understanding of AvPD within the broader context of Cluster C personality disorders, emphasizing critical thinking, synthesis of information, and the ability to distinguish AvPD from similar conditions.\",\"objectives\":[\"To critically analyze the clinical features and diagnostic criteria of Avoidant Personality Disorder (AvPD).\",\"To differentiate AvPD from similar psychological conditions, specifically Schizoid Personality Disorder and Social Phobia, based on key distinguishing factors.\",\"To investigate the causal factors contributing to AvPD, including biological predispositions and environmental influences, within a diathesis-stress framework.\",\"To research and propose evidence-based treatment strategies for AvPD, considering its overlap with social phobia.\",\"To synthesize information from the provided document and external academic sources to construct a well-supported and coherent argument.\"],\"tasks\":[{\"task_number\":1,\"task_title\":\"Clinical Presentation and Diagnostic Criteria of AvPD\",\"description\":\"Based on the provided document, describe the core clinical features of Avoidant Personality Disorder (AvPD), including extreme social inhibition, desire for affection despite fear of rejection, low self-esteem, and avoidance of novel experiences. Subsequently, outline the DSM-5 diagnostic criteria for AvPD, explaining how these criteria manifest in a clinical context. Provide specific examples for each criterion where applicable.\",\"word_count\":250},{\"task_number\":2,\"task_title\":\"Differential Diagnosis: AvPD vs. Schizoid Personality Disorder and Social Phobia\",\"description\":\"Conduct a comparative analysis to clearly differentiate Avoidant Personality Disorder from both Schizoid Personality Disorder and Social Phobia (Social Anxiety Disorder). Utilize the distinctions highlighted in the document, such as reasons for social withdrawal, desire for relationships, emotional experience, and the pervasiveness of symptoms. Supplement this with additional research to elaborate on the nuances and potential challenges in differential diagnosis, particularly regarding the \'severity\' argument for AvPD as a form of social phobia.\",\"word_count\":300},{\"task_number\":3,\"task_title\":\"Causal Factors and the Diathesis-Stress Model in AvPD\",\"description\":\"Elaborate on the causal factors of AvPD as presented in the document, focusing on the concepts of \'inhibited temperament,\' \'genetic vulnerability,\' and \'heritable traits.\' Explain how these biological predispositions interact with early emotional experiences (e.g., emotional abuse, critical parenting) within a diathesis-stress model to contribute to the development of AvPD. Provide a detailed explanation of how biology creates vulnerability and environment activates it.\",\"word_count\":250},{\"task_number\":4,\"task_title\":\"Treatment Approaches for Avoidant Personality Disorder\",\"description\":\"Based on the document\'s assertion that AvPD treatment often resembles social phobia treatment, research and describe at least three specific therapeutic techniques or modalities commonly used for social phobia that would be applicable and effective for individuals with AvPD. For each technique, explain its rationale and how it addresses the core symptoms of AvPD (e.g., fear of criticism, social anxiety, avoidance). You must cite at least two external academic sources for this task.\",\"word_count\":200}],\"evaluation_criteria\":[{\"criterion\":\"Content Quality and Accuracy\",\"weight\":\"30%\",\"description\":\"Accuracy of information, depth of understanding of AvPD concepts, and correct application of psychological terminology. Evidence of critical thinking and synthesis of information.\"},{\"criterion\":\"Comparative Analysis and Differentiation\",\"weight\":\"25%\",\"description\":\"Clarity and thoroughness in distinguishing AvPD from Schizoid Personality Disorder and Social Phobia. Ability to identify and explain key differentiating factors effectively.\"},{\"criterion\":\"Research and Integration of External Sources\",\"weight\":\"20%\",\"description\":\"Effectiveness in integrating information from the provided document with external academic research (for Task 2 and 4). Proper citation and referencing of all sources.\"},{\"criterion\":\"Structure, Clarity, and Cohesion\",\"weight\":\"15%\",\"description\":\"Organization of the assignment, logical flow of arguments, clarity of expression, and overall coherence. Adherence to word counts for each task.\"},{\"criterion\":\"Adherence to Assignment Requirements\",\"weight\":\"10%\",\"description\":\"Compliance with all formatting, submission, and task-specific instructions.\"}],\"resources\":[\"American Psychiatric Association. (2013). Diagnostic and statistical manual of mental disorders (5th ed.).\",\"Millon, T., & Martinez, A. (1995). The Millon Clinical Multiaxial Inventory-III (MCMI-III): A review and critique. Journal of Personality Assessment, 65(3), 401-419. (Or similar foundational texts on personality disorders)\",\"Sanislow, C. A., Grilo, C. M., Morey, L. C., Gunderson, J. G., Shea, M. T., Zanarini, M. C., ... & McGlashan, T. H. (2012). Confirmatory factor analysis of the DSM-IV criteria for avoidant personality disorder. Journal of Personality Disorders, 26(2), 237-250.\",\"Taylor, S., Jang, K. L., & Livesley, W. J. (2004). The relationship between avoidant personality disorder and social phobia. Journal of Anxiety Disorders, 18(4), 433-442.\",\"Carter, J. D., & Wu, J. (2010). Avoidant personality disorder and social phobia: A review of the literature. Clinical Psychology Review, 30(2), 149-161.\",\"Academic databases (e.g., PsycINFO, PubMed, Google Scholar) for peer-reviewed articles on AvPD treatment and differential diagnosis.\"],\"submission_guidelines\":[\"Submit your assignment as a single document (e.g., PDF or Word document).\",\"Ensure all tasks are clearly numbered and titled.\",\"Include a reference list at the end of the document, using APA 7th edition style.\",\"Adhere to the specified word counts for each task; deviations of +\\/- 10% are acceptable.\",\"Plagiarism will not be tolerated. All external information must be properly cited.\"],\"due_date\":\"Due in 2 weeks\",\"total_marks\":100}}', 1, '2026-03-11 02:26:11', '2026-03-11 07:55:50', '2026-03-11 07:56:11', 2);

-- --------------------------------------------------------

--
-- Table structure for table `ai_chat_messages`
--

CREATE TABLE `ai_chat_messages` (
  `id` int NOT NULL,
  `session_id` int NOT NULL,
  `role` enum('user','assistant','system') COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_chat_sessions`
--

CREATE TABLE `ai_chat_sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `resource_id` int DEFAULT NULL,
  `session_name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_question_papers`
--

CREATE TABLE `ai_question_papers` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `resource_id` int DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `subject_id` int DEFAULT NULL,
  `total_marks` int DEFAULT '100',
  `duration_minutes` int DEFAULT '180',
  `format_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `paper_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_published` tinyint(1) DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `ai_question_papers`
--

INSERT INTO `ai_question_papers` (`id`, `user_id`, `resource_id`, `title`, `subject_id`, `total_marks`, `duration_minutes`, `format_config`, `paper_data`, `is_published`, `published_at`, `created_at`) VALUES
(1, 6, 10, 'midterm', 7, 100, 180, NULL, '{\n  \"sections\": [\n    {\n      \"section_name\": \"Section A\",\n      \"questions\": [\n        {\n          \"question_number\": 1,\n          \"question_text\": \"According to the text, what is a direct physical impact of stress?\",\n          \"marks\": 2,\n          \"type\": \"mcq\",\n          \"options\": {\n            \"A\": \"Improved cognitive function\",\n            \"B\": \"Increased heart rate and sweating\",\n            \"C\": \"Enhanced immune system\",\n            \"D\": \"Decreased blood pressure\"\n          },\n          \"correct_answer\": \"B\"\n        },\n        {\n          \"question_number\": 2,\n          \"question_text\": \"The brain\'s role in stress responses includes influencing which bodily system, making one more susceptible to illnesses?\",\n          \"marks\": 2,\n          \"type\": \"mcq\",\n          \"options\": {\n            \"A\": \"Digestive system\",\n            \"B\": \"Skeletal system\",\n            \"C\": \"Immune system\",\n            \"D\": \"Reproductive system\"\n          },\n          \"correct_answer\": \"C\"\n        },\n        {\n          \"question_number\": 3,\n          \"question_text\": \"How an individual perceives their problems significantly influences their physical health. Which of the following is an example provided?\",\n          \"marks\": 2,\n          \"type\": \"mcq\",\n          \"options\": {\n            \"A\": \"Eating healthy food to reduce stress.\",\n            \"B\": \"Two people reacting differently to job loss, one with headaches and fatigue, the other seeing opportunity.\",\n            \"C\": \"Regular exercise to improve mood.\",\n            \"D\": \"Taking medication to lower blood pressure.\"\n          },\n          \"correct_answer\": \"B\"\n        },\n        {\n          \"question_number\": 4,\n          \"question_text\": \"Which of the following is described as a healthy coping mechanism that can reduce stress\'s impact on physical health?\",\n          \"marks\": 2,\n          \"type\": \"mcq\",\n          \"options\": {\n            \"A\": \"Substance abuse\",\n            \"B\": \"Excessive drinking\",\n            \"C\": \"Regular physical activity\",\n            \"D\": \"Smoking\"\n          },\n          \"correct_answer\": \"C\"\n        },\n        {\n          \"question_number\": 5,\n          \"question_text\": \"Acute stress, a short-term reaction, can trigger serious physical health issues, especially for individuals with pre-existing conditions. What example is given?\",\n          \"marks\": 2,\n          \"type\": \"mcq\",\n          \"options\": {\n            \"A\": \"Improved sleep quality after a short argument.\",\n            \"B\": \"A person with heart disease experiencing a heart attack after an intense argument.\",\n            \"C\": \"Reduced blood pressure during a stressful event.\",\n            \"D\": \"Decreased muscle pain during a sudden challenge.\"\n          },\n          \"correct_answer\": \"B\"\n        },\n        {\n          \"question_number\": 6,\n          \"question_text\": \"Besides heart problems, which of these is NOT listed as an acute stress symptom?\",\n          \"marks\": 2,\n          \"type\": \"mcq\",\n          \"options\": {\n            \"A\": \"Increased blood pressure\",\n            \"B\": \"Headaches\",\n            \"C\": \"Muscle pain\",\n            \"D\": \"Enhanced digestion\"\n          },\n          \"correct_answer\": \"D\"\n        },\n        {\n          \"question_number\": 7,\n          \"question_text\": \"Chronic stress leads to the continuous overactivation of which bodily system?\",\n          \"marks\": 2,\n          \"type\": \"mcq\",\n          \"options\": {\n            \"A\": \"Central Nervous System\",\n            \"B\": \"Autonomic Nervous System\",\n            \"C\": \"Somatic Nervous System\",\n            \"D\": \"Endocrine System\"\n          },\n          \"correct_answer\": \"B\"\n        },\n        {\n          \"question_number\": 8,\n          \"question_text\": \"Which of the following health problems is NOT directly associated with chronic stress in the text?\",\n          \"marks\": 2,\n          \"type\": \"mcq\",\n          \"options\": {\n            \"A\": \"Hypertension\",\n            \"B\": \"Cardiovascular Diseases\",\n            \"C\": \"Asthma\",\n            \"D\": \"Type 2 Diabetes\"\n          },\n          \"correct_answer\": \"C\"\n        },\n        {\n          \"question_number\": 9,\n          \"question_text\": \"A person under constant work stress experiencing frequent headaches, muscle aches, and trouble sleeping is an example of what?\",\n          \"marks\": 2,\n          \"type\": \"mcq\",\n          \"options\": {\n            \"A\": \"Improved physical resilience\",\n            \"B\": \"Stress affecting the body\",\n            \"C\": \"Healthy coping mechanisms\",\n            \"D\": \"Lack of physical activity\"\n          },\n          \"correct_answer\": \"B\"\n        },\n        {\n          \"question_number\": 10,\n          \"question_text\": \"While physical health impacts are important, what is the ultimate focus of the unit regarding stress?\",\n          \"marks\": 2,\n          \"type\": \"mcq\",\n          \"options\": {\n            \"A\": \"Dietary interventions\",\n            \"B\": \"Physical exercise routines\",\n            \"C\": \"Mental health and how stress leads to trauma and stress-related disorders\",\n            \"D\": \"Cardiovascular health monitoring\"\n          },\n          \"correct_answer\": \"C\"\n        }\n      ]\n    },\n    {\n      \"section_name\": \"Section B\",\n      \"questions\": [\n        {\n          \"question_number\": 11,\n          \"question_text\": \"Explain how an individual\'s perception of stress can influence their physical health, providing a specific example from the text.\",\n          \"marks\": 5,\n          \"type\": \"short\"\n        },\n        {\n          \"question_number\": 12,\n          \"question_text\": \"Differentiate between the immediate physical reactions to \'acute stress\' and the long-term consequences of \'chronic stress\' based on the provided content.\",\n          \"marks\": 5,\n          \"type\": \"short\"\n        },\n        {\n          \"question_number\": 13,\n          \"question_text\": \"List and briefly describe three common physical symptoms of stress mentioned in the text, excluding cardiovascular issues.\",\n          \"marks\": 5,\n          \"type\": \"short\"\n        },\n        {\n          \"question_number\": 14,\n          \"question_text\": \"Discuss the role of coping mechanisms in managing stress, contrasting healthy and unhealthy strategies and their respective impacts on physical health.\",\n          \"marks\": 5,\n          \"type\": \"short\"\n        },\n        {\n          \"question_number\": 15,\n          \"question_text\": \"Describe the brain\'s involvement in the link between stress and physical health, particularly its influence on the immune system.\",\n          \"marks\": 5,\n          \"type\": \"short\"\n        }\n      ]\n    },\n    {\n      \"section_name\": \"Section C\",\n      \"questions\": [\n        {\n          \"question_number\": 16,\n          \"question_text\": \"Elaborate on the various long-term health effects associated with chronic stress. Provide detailed explanations and examples for at least three distinct conditions mentioned in the text.\",\n          \"marks\": 15,\n          \"type\": \"long\"\n        },\n        {\n          \"question_number\": 17,\n          \"question_text\": \"Discuss the comprehensive impact of stress on physical health, covering both acute and chronic manifestations. Explain how psychological factors such as perception of stress and coping mechanisms significantly influence these physical outcomes.\",\n          \"marks\": 15,\n          \"type\": \"long\"\n        },\n        {\n          \"question_number\": 18,\n          \"question_text\": \"While the provided unit primarily focuses on mental health, describe in detail the extensive physical health impacts of stress, using specific examples from the text. Conclude by briefly explaining the overarching mental health connection mentioned in the \'Focus of the Unit\' section.\",\n          \"marks\": 15,\n          \"type\": \"long\"\n        }\n      ]\n    }\n  ]\n}', 1, '2026-03-10 16:24:58', '2026-03-10 21:54:29');

-- --------------------------------------------------------

--
-- Table structure for table `ai_quizzes`
--

CREATE TABLE `ai_quizzes` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `resource_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `difficulty` enum('easy','medium','hard') COLLATE utf8mb4_general_ci DEFAULT 'medium',
  `num_questions` int DEFAULT '10',
  `quiz_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_published` tinyint(1) DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `ai_quizzes`
--

INSERT INTO `ai_quizzes` (`id`, `user_id`, `resource_id`, `subject_id`, `title`, `difficulty`, `num_questions`, `quiz_data`, `is_published`, `published_at`, `created_at`) VALUES
(1, 6, 9, 7, 'quiz1', 'medium', 10, '{\"questions\":[{\"question\":\"What is the common theme shared by all Cluster C personality disorders?\",\"options\":[\"A) Grandiosity and attention-seeking\",\"B) Emotional instability and impulsivity\",\"C) Anxiety, fearfulness, and excessive worry\",\"D) Detachment and eccentric behavior\"],\"correct_answer\":\"C\",\"explanation\":\"The text explicitly states, \'The common theme across this cluster is anxiety, fearfulness, and excessive worry.\'\"},{\"question\":\"Which of the following is a core characteristic of Avoidant Personality Disorder (AvPD)?\",\"options\":[\"A) A pervasive pattern of disregard for the rights of others.\",\"B) Extreme social discomfort, hypersensitivity to criticism, and strong fears of rejection.\",\"C) An excessive need to be taken care of, leading to submissive and clinging behavior.\",\"D) Preoccupation with orderliness, perfectionism, and control.\"],\"correct_answer\":\"B\",\"explanation\":\"The text defines AvPD as \'characterized by extreme social discomfort, hypersensitivity to criticism, and strong fears of rejection.\'\"},{\"question\":\"A key difference between Avoidant Personality Disorder (AvPD) and Schizoid Personality Disorder regarding relationships is that individuals with AvPD:\",\"options\":[\"A) Genuinely prefer solitude and have no desire for relationships.\",\"B) Crave closeness and intimate relationships but avoid them due to fear of rejection.\",\"C) Actively seek out many superficial relationships to avoid loneliness.\",\"D) Are indifferent to social interaction and emotional expression.\"],\"correct_answer\":\"B\",\"explanation\":\"The text states that people with AvPD \'crave closeness, acceptance, and intimate relationships\' but \'avoid people because they are terrified of criticism, disapproval, or humiliation,\' unlike those with Schizoid PD who prefer solitude.\"},{\"question\":\"Individuals with Avoidant Personality Disorder (AvPD) typically hold which of the following beliefs about themselves?\",\"options\":[\"A) \\\"I am superior to others and deserve special treatment.\\\"\",\"B) \\\"I am socially incompetent and not good enough.\\\"\",\"C) \\\"I am capable of anything, but others hold me back.\\\"\",\"D) \\\"I am perfectly content being alone and don\'t need others.\\\"\"],\"correct_answer\":\"B\",\"explanation\":\"The text explicitly lists beliefs such as \'I am socially incompetent,\' \'Others will definitely reject me,\' and \'I am not good enough.\'\"},{\"question\":\"How does Avoidant Personality Disorder (AvPD) primarily differ from Social Phobia (Social Anxiety Disorder)?\",\"options\":[\"A) AvPD is less severe and more situational than Social Phobia.\",\"B) Social Phobia involves a more deeply impaired self-image than AvPD.\",\"C) AvPD is more pervasive, affecting most life domains, and involves enduring personality traits.\",\"D) Social Phobia involves avoidance of both positive and negative emotional experiences, unlike AvPD.\"],\"correct_answer\":\"C\",\"explanation\":\"The text states that \'AvPD is more pervasive, affecting most life domains\' and \'The symptoms are enduring personality traits, not situational fears,\' distinguishing it from Social Phobia.\"},{\"question\":\"According to DSM-5 criteria, how many specific symptoms are required for a diagnosis of Avoidant Personality Disorder (AvPD)?\",\"options\":[\"A) Two or more\",\"B) Three or more\",\"C) Four or more\",\"D) Five or more\"],\"correct_answer\":\"C\",\"explanation\":\"The DSM-5 criteria section explicitly states, \'A diagnosis requires four (or more) of the following.\'\"},{\"question\":\"Which of the following is a DSM-5 criterion for Avoidant Personality Disorder (AvPD)?\",\"options\":[\"A) A pattern of unstable and intense interpersonal relationships.\",\"B) Recurrent suicidal behavior, gestures, or threats.\",\"C) Avoids occupational tasks that involve significant social contact due to fears of criticism.\",\"D) Grandiose sense of self-importance.\"],\"correct_answer\":\"C\",\"explanation\":\"\'Avoids interpersonal work activities\' is listed as the first DSM-5 criterion for AvPD.\"},{\"question\":\"What does an \\\"inhibited temperament\\\" as a causal factor for Avoidant Personality Disorder (AvPD) primarily refer to?\",\"options\":[\"A) A learned behavior from overly protective parenting.\",\"B) A biological tendency to feel uneasy or overwhelmed in unfamiliar social situations from birth.\",\"C) A conscious decision to avoid social interaction due to past negative experiences.\",\"D) A preference for solitude developed in adolescence.\"],\"correct_answer\":\"B\",\"explanation\":\"The text describes an \'inborn “Inhibited” Temperament\' as a \'biological tendency to feel uneasy or overwhelmed in unfamiliar social situations.\'\"},{\"question\":\"Regarding genetic vulnerability, what is suggested about Avoidant Personality Disorder (AvPD) and Social Phobia?\",\"options\":[\"A) They have entirely different genetic underpinnings.\",\"B) AvPD is solely caused by environmental factors, with no genetic component.\",\"C) They share partly similar genetic vulnerabilities, meaning some genes increase risk for both.\",\"D) Social Phobia is a prerequisite for developing AvPD due to genetic factors.\"],\"correct_answer\":\"C\",\"explanation\":\"The text states that AvPD and Social Phobia \'share partly similar genetic vulnerabilities.\'\"},{\"question\":\"In the context of Avoidant Personality Disorder (AvPD), how does the diathesis-stress model explain its development?\",\"options\":[\"A) Stress alone is sufficient to cause AvPD, regardless of biological predisposition.\",\"B) A biological vulnerability (diathesis) interacts with environmental stressors to activate the disorder.\",\"C) Diathesis refers solely to environmental factors, and stress is the biological component.\",\"D) AvPD is entirely genetic, with no role for environmental stress.\"],\"correct_answer\":\"B\",\"explanation\":\"The text explains the diathesis-stress model as \'Biology creates the vulnerability → Environment activates it,\' where the inhibited temperament is the diathesis.\"}]}', 1, '2026-03-16 00:26:05', '2026-03-16 05:55:59');

-- --------------------------------------------------------

--
-- Table structure for table `ai_usage_logs`
--

CREATE TABLE `ai_usage_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `feature_type` enum('chat','quiz','question_paper','summary') COLLATE utf8mb4_general_ci NOT NULL,
  `resource_id` int DEFAULT NULL,
  `tokens_used` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `subject_id` int NOT NULL,
  `faculty_id` int NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `max_marks` int DEFAULT '100',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `code`, `description`, `created_at`, `updated_at`) VALUES
(1, 'BTech AI and ML', 'BTECH-AI', 'Bachelor of Technology in Artificial Intelligence and Machine Learning', '2026-02-16 10:26:57', '2026-02-16 10:26:57'),
(2, 'BSc in Psychology', 'BSC-PSY', 'Bachelor of Science in Psychology', '2026-02-16 10:26:57', '2026-02-16 10:26:57'),
(3, 'BTech in Sound Engineering', 'BTECH-SE', 'Bachelor of Technology in Sound Engineering', '2026-02-16 10:26:57', '2026-02-16 10:26:57'),
(4, 'BBA', 'BBA', 'Bachelor of Business Administration', '2026-02-16 10:26:57', '2026-02-16 10:26:57'),
(5, 'MBA', 'MBA', 'Master of Business Administration', '2026-02-16 10:26:57', '2026-02-16 10:26:57');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `employee_id` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `department` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `user_id`, `department_id`, `employee_id`, `department`) VALUES
(1, 2, NULL, 'FAC001', 'Computer Science'),
(2, 4, 4, NULL, NULL),
(3, 6, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_departments`
--

CREATE TABLE `faculty_departments` (
  `id` int NOT NULL,
  `faculty_id` int NOT NULL,
  `department_id` int NOT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_departments`
--

INSERT INTO `faculty_departments` (`id`, `faculty_id`, `department_id`, `assigned_at`) VALUES
(2, 3, 2, '2026-02-16 11:40:15'),
(3, 2, 4, '2026-02-16 11:52:07'),
(4, 2, 1, '2026-02-16 11:52:07'),
(5, 2, 5, '2026-02-16 11:52:07');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_subjects`
--

CREATE TABLE `faculty_subjects` (
  `id` int NOT NULL,
  `faculty_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_subjects`
--

INSERT INTO `faculty_subjects` (`id`, `faculty_id`, `subject_id`, `assigned_at`) VALUES
(1, 1, 1, '2026-02-06 03:50:12'),
(2, 1, 4, '2026-02-06 03:50:12'),
(3, 1, 6, '2026-02-06 03:50:12'),
(4, 3, 7, '2026-02-06 09:56:45'),
(5, 2, 4, '2026-02-06 10:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `version` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`version`) VALUES
(5);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `correct_answers` int NOT NULL,
  `total_questions` int NOT NULL,
  `time_taken` int NOT NULL,
  `attempted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `file_path` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_size` int DEFAULT '0',
  `original_filename` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subject_id` int NOT NULL,
  `semester` int DEFAULT '1',
  `uploaded_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1',
  `faculty_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `title`, `description`, `file_path`, `file_type`, `file_size`, `original_filename`, `subject_id`, `semester`, `uploaded_by`, `created_at`, `is_active`, `faculty_id`) VALUES
(1, 'Clinical Pychology Reference Book', 'Reference Book', 'uploads/resources/resource_1770719392_698b08a0862e1.pdf', 'pdf', 3263, 'James E. Maddux, Barbara A. Winstead (eds.)-Psychopathology_ Foundations for a Contemporary Understanding-Routledge (2016).pdf', 7, 4, 6, '2026-02-10 10:29:52', 1, NULL),
(2, 'DSM 5 Explainer Book', '', 'uploads/resources/resource_1770719510_698b0916dfc73.pdf', 'pdf', 3579, 'DSM 5 Made Eaasy.pdf', 7, 4, 6, '2026-02-10 10:31:50', 1, NULL),
(3, 'Personality Disorders Reference Book', '', 'uploads/resources/resource_1770719708_698b09dc96b9f.pdf', 'pdf', 2553, 'Personality Disorders - Mario Maj.pdf', 7, 4, 6, '2026-02-10 10:35:08', 1, NULL),
(4, 'DSM 5 TR Original text', '', 'uploads/resources/resource_1770719761_698b0a119b06f.pdf', 'pdf', 8666, 'DSM 5 TR.pdf', 7, 4, 6, '2026-02-10 10:36:01', 1, NULL),
(5, 'Substance Abuse Prevention, Treatment and Recovery', '', 'uploads/resources/resource_1770720421_698b0ca59226c.pdf', 'pdf', 18425, 'Substance Abuse Prevention,Treatment And Recovery.pdf', 7, 4, 6, '2026-02-10 10:47:01', 1, NULL),
(6, 'Module 1 Notes: Introduction to Personality and Personality Disorders', '', 'uploads/resources/resource_1770770053_698bce85ac337.pdf', 'pdf', 167, '1.1_SLABSPSY6.02005_Clinical_Psychology_–_II__Module_1_-_Introduction_to_Personality_and_personality_Disorders.pdf', 7, 4, 6, '2026-02-11 00:34:13', 1, NULL),
(7, 'Module 1 Notes: Cluster A Personality Disorders', '', 'uploads/resources/resource_1770770092_698bceac8e645.pdf', 'pdf', 199, 'SLABSPSY6.02005 Clinical Psychology – II  Module 1 - Cluster A Disorders.pdf', 7, 4, 6, '2026-02-11 00:34:52', 1, NULL),
(8, 'Module 1 Notes: Cluster B Personality Disorders', '', 'uploads/resources/resource_1770770121_698bcec9c37a2.pdf', 'pdf', 173, 'SLABSPSY6.02005 Clinical Psychology – II  Module 1 - Cluster B Disorders.pdf', 7, 4, 6, '2026-02-11 00:35:21', 1, NULL),
(9, 'Module 1 Notes: Cluster C Personality Disorders', '', 'uploads/resources/resource_1770770147_698bcee3467a6.pdf', 'pdf', 208, 'SLABSPSY6.02005 Clinical Psychology – II  Module 1 -  Cluster C Disorders.pdf', 7, 4, 6, '2026-02-11 00:35:47', 1, NULL),
(10, 'Module 2: Trauma & Stress related Disorders and Somatoform Disorders', '', 'uploads/resources/resource_1770770185_698bcf094dd10.pdf', 'pdf', 229, 'Module 2 Trauma & Stress related Disorders and Somatoform Disorders.pdf', 7, 4, 6, '2026-02-11 00:36:25', 1, NULL),
(11, 'personality', '', 'uploads/resources/resource_1772793612_69aaaf0c8da67.pdf', 'pdf', 208, 'SLABSPSY6.02005_Clinical_Psychology_____II__Module_1_-__Cluster_C_Disorders.pdf', 7, 4, 6, '2026-03-06 05:10:12', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `student_id` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `current_semester` int NOT NULL,
  `enrollment_year` year DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `department_id`, `student_id`, `current_semester`, `enrollment_year`) VALUES
(1, 3, 2, 'STU001', 2, '2025'),
(2, 5, 2, NULL, 8, '2026'),
(4, 8, 2, NULL, 4, '2024'),
(5, 9, 2, NULL, 4, '2024'),
(6, 10, 2, NULL, 4, '2024'),
(7, 11, 2, NULL, 4, '2024');

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollments`
--

CREATE TABLE `student_enrollments` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `enrolled_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_enrollments`
--

INSERT INTO `student_enrollments` (`id`, `student_id`, `subject_id`, `enrolled_at`, `is_active`) VALUES
(1, 3, 1, '2026-02-06 03:50:12', 1),
(2, 3, 4, '2026-02-06 03:50:12', 1),
(3, 3, 6, '2026-02-06 03:50:12', 1),
(4, 1, 7, '2026-03-11 08:04:16', 1),
(5, 4, 7, '2026-03-11 08:04:16', 1),
(6, 5, 7, '2026-03-11 08:04:16', 1),
(7, 6, 7, '2026-03-11 08:04:16', 1),
(8, 7, 7, '2026-03-11 08:04:16', 1),
(9, 2, 7, '2026-03-11 08:04:16', 1),
(10, 3, 7, '2026-03-11 08:05:59', 1);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int NOT NULL,
  `subject_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `subject_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `department_id` int DEFAULT NULL,
  `semester` int NOT NULL,
  `credits` int DEFAULT '3',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `department_id`, `semester`, `credits`, `created_at`, `is_active`) VALUES
(1, 'MATH101', 'Mathematics', 1, 1, 3, '2026-02-06 03:50:12', 1),
(2, 'PHYS101', 'Physics', 1, 1, 3, '2026-02-06 03:50:12', 1),
(3, 'CHEM101', 'Chemistry', 1, 3, 3, '2026-02-06 03:50:12', 1),
(4, 'CS101', 'Computer Science', 1, 1, 4, '2026-02-06 03:50:12', 1),
(5, 'ENG101', 'English', 1, 1, 3, '2026-02-06 03:50:12', 1),
(6, 'DL002', 'Deep Learning', 1, 2, 3, '2026-02-06 03:50:12', 1),
(7, 'SLABSPSY6.02005', 'Clinical Psychology', 2, 4, 2, '2026-02-06 09:53:32', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','faculty','student') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`, `updated_at`, `last_login`, `is_active`) VALUES
(1, 'admin', 'admin@college.edu', '$2y$12$wkc.02EbqNi7o5oPGibvxOCpYwfdoDHhJN/yZzw2h7pNdXuziUIAu', 'admin', '2026-02-06 03:50:12', '2026-02-06 03:50:12', NULL, 1),
(2, 'faculty_demo', 'faculty@college.edu', '$2y$12$wSL2BPre8mMcAI5qycnHMeiSO6ty93ULVhu1k3gQaStZcXl07SFou', 'faculty', '2026-02-06 03:50:12', '2026-02-06 03:50:12', NULL, 1),
(3, 'student_demo', 'student@college.edu', '$2y$12$HBZBwEkqSdgc9Gi.tGR/cuiYTN8qfZk.mN8GMBOHLBqYJWsVnOtTe', 'student', '2026-02-06 03:50:12', '2026-02-06 03:50:12', NULL, 1),
(4, 'ftest1', 'ftest1@test.com', '$2y$10$2g8SFnpFQyTP06TWpXZ0BOQQEouB1griR1.JCaCiixNCeVKOcXtxS', 'faculty', '2026-02-06 03:52:09', '2026-02-06 03:52:09', NULL, 1),
(5, 'stest1', 'stest1@test.com', '$2y$10$LAa7W2o8KbhoFUbMNGvJkermBbGxgmjLnQQcSmr7KmNaku7jAq8BO', 'student', '2026-02-06 03:52:37', '2026-02-06 03:52:37', NULL, 1),
(6, 'psychology', 'psychology@uai.com', '$2y$10$HlWeg7CiRitgbi/53v8LBOwkYzTOzLqcruvehDf/JpKQQ2GVOt70u', 'faculty', '2026-02-06 03:57:35', '2026-02-06 03:57:35', NULL, 1),
(7, 'Arshia.Agicha', 'arshia.agicha@universalai.in', '$2y$10$LYqrLeWvM.X9gmgPm7yiluedXb5AQmJtjw5xGtBtNriGLvwYyCv3e', 'student', '2026-02-06 10:56:58', '2026-02-06 10:56:58', NULL, 1),
(8, 'Diya.Kakde', 'diya.kakde@universalai.in', '$2y$10$Dj.mHZeKy7ygyp7e/y8NJ.gmKpkH3jgoYOPv24P98nQWFE7SUWQOa', 'student', '2026-02-06 10:57:37', '2026-02-06 10:57:37', NULL, 1),
(9, 'Pari.Hamirwasia', 'pari.hamirwasia@universalai.in', '$2y$10$CP5o6NT/Ht2IQZdbERnaQOZTrdAXFUVQLnVhG2f5ag02rYASdQ4Qu', 'student', '2026-02-06 10:57:37', '2026-02-06 10:57:37', NULL, 1),
(10, 'Sayali.Dhumal', 'taqee.shaikh@universalai.in', '$2y$10$/gX3ljI60a3fIXCA6tGCBurPoTdkQPn22nSBxqI.H7skz0bENh3m.', 'student', '2026-02-06 10:57:37', '2026-02-06 10:57:37', NULL, 1),
(11, 'Taqee.Shaikh', 'sayali.dhumal@universalai.in', '$2y$10$D2Wn0ObtVsPBIm4DzS4HO.qC0Yf2kSG1dQHCQP37hH.YZYxD9P.hq', 'student', '2026-02-06 10:57:37', '2026-02-06 10:57:37', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_assignments`
--
ALTER TABLE `ai_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_resource` (`resource_id`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_published` (`is_published`);

--
-- Indexes for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `ai_chat_sessions`
--
ALTER TABLE `ai_chat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_resource` (`resource_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `ai_question_papers`
--
ALTER TABLE `ai_question_papers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resource_id` (`resource_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_published` (`is_published`);

--
-- Indexes for table `ai_quizzes`
--
ALTER TABLE `ai_quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_resource` (`resource_id`),
  ADD KEY `idx_subject_id` (`subject_id`),
  ADD KEY `idx_published` (`is_published`);

--
-- Indexes for table `ai_usage_logs`
--
ALTER TABLE `ai_usage_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resource_id` (`resource_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_feature` (`feature_type`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `fk_assignments_faculty` (`faculty_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `fk_faculty_user` (`user_id`),
  ADD KEY `fk_faculty_department` (`department_id`);

--
-- Indexes for table `faculty_departments`
--
ALTER TABLE `faculty_departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_faculty_department` (`faculty_id`,`department_id`),
  ADD KEY `idx_faculty` (`faculty_id`),
  ADD KEY `idx_department` (`department_id`);

--
-- Indexes for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`faculty_id`,`subject_id`),
  ADD KEY `idx_faculty` (`faculty_id`),
  ADD KEY `idx_subject` (`subject_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_quiz` (`quiz_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_uploader` (`uploaded_by`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_semester` (`current_semester`),
  ADD KEY `fk_student_user` (`user_id`),
  ADD KEY `fk_student_department` (`department_id`);

--
-- Indexes for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`subject_id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`),
  ADD KEY `idx_subject_code` (`subject_code`),
  ADD KEY `idx_semester` (`semester`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `fk_subject_department` (`department_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_active` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_assignments`
--
ALTER TABLE `ai_assignments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_chat_sessions`
--
ALTER TABLE `ai_chat_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_question_papers`
--
ALTER TABLE `ai_question_papers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_quizzes`
--
ALTER TABLE `ai_quizzes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_usage_logs`
--
ALTER TABLE `ai_usage_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `faculty_departments`
--
ALTER TABLE `faculty_departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_assignments`
--
ALTER TABLE `ai_assignments`
  ADD CONSTRAINT `ai_assignments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_assignments_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_assignments_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  ADD CONSTRAINT `ai_chat_messages_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `ai_chat_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_chat_sessions`
--
ALTER TABLE `ai_chat_sessions`
  ADD CONSTRAINT `ai_chat_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_chat_sessions_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_question_papers`
--
ALTER TABLE `ai_question_papers`
  ADD CONSTRAINT `ai_question_papers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_question_papers_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_question_papers_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_quizzes`
--
ALTER TABLE `ai_quizzes`
  ADD CONSTRAINT `ai_quizzes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_quizzes_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_usage_logs`
--
ALTER TABLE `ai_usage_logs`
  ADD CONSTRAINT `ai_usage_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_usage_logs_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_assignments_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`),
  ADD CONSTRAINT `fk_assignments_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_faculty_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_faculty_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  ADD CONSTRAINT `faculty_subjects_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faculty_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `ai_quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `fk_resources_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resources_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resources_ibfk_3` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  ADD CONSTRAINT `student_enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_enrollments_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
