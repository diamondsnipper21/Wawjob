<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categories')->delete();
        
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 0,
                'name' => '<EN>Web Development</EN><kp>웨브개발</kp><ch>网站发展</ch>',
                'desc' => 'Web Development',
                'parent_id' => 0,
                'order' => 0,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 0,
                'name' => '<en>Web Design</en><KP>웹 디자인</KP><CH>网页设计</CH>',
                'desc' => 'Web Design',
                'parent_id' => 1,
                'order' => 0,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 0,
                'name' => '<en>Web Development</en><KP>웨브개발</KP><CH>网页开发</CH>',
                'desc' => 'Web Development',
                'parent_id' => 1,
                'order' => 1,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 4,
                'type' => 0,
                'name' => '<en>QA & Testing</en><KP>품질검사</KP><CH>质量保证和测试</CH>',
                'desc' => 'QA & Testing',
                'parent_id' => 1,
                'order' => 2,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 5,
                'type' => 0,
                'name' => '<en>Other</en><KP>기타</KP><CH>其他</CH>',
                'desc' => 'Other',
                'parent_id' => 1,
                'order' => 3,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 9,
                'type' => 0,
                'name' => '<en>Mobile & Software Dev</en><KP>모바일 & 소프트웨어 개발</KP><CH>移动和软件开发</CH>',
                'desc' => 'Mobile & Software Dev',
                'parent_id' => 0,
                'order' => 1,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 10,
                'type' => 0,
                'name' => '<en>Mobile Design</en><KP>모바일 디자인</KP><CH>移动设计</CH>',
                'desc' => 'Mobile Design',
                'parent_id' => 9,
                'order' => 0,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 11,
                'type' => 0,
                'name' => '<en>Mobile Development</en><KP>모바일 개발</KP><CH>移动开发</CH>',
                'desc' => 'Mobile Development',
                'parent_id' => 9,
                'order' => 1,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 12,
                'type' => 0,
                'name' => '<en>Desktop Software Dev</en><KP>PC 소프트웨어 개발</KP><CH>桌面软件开发</CH>',
                'desc' => 'Desktop Software Dev',
                'parent_id' => 9,
                'order' => 2,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 13,
                'type' => 0,
                'name' => '<en>Game Development</en><KP>게임개발</KP><CH>游戏开发</CH>',
                'desc' => 'Game Development',
                'parent_id' => 9,
                'order' => 3,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 14,
                'type' => 0,
                'name' => '<en>QA & Testing</en><KP>품질검사</KP><CH>质量保证和测试</CH>',
                'desc' => 'QA & Testing',
                'parent_id' => 9,
                'order' => 4,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 15,
                'type' => 0,
                'name' => '<en>Other</en><KP>기타</KP><CH>其他</CH>',
                'desc' => 'Other',
                'parent_id' => 9,
                'order' => 5,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 20,
                'type' => 0,
                'name' => '<en>Design & Creative</en><KP>디자인 과 창조</KP><CH>设计与创意</CH>',
                'desc' => 'Design & Creative',
                'parent_id' => 0,
                'order' => 2,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 21,
                'type' => 0,
                'name' => '<en>Animation</en><KP>만화</KP><CH>动画</CH>',
                'desc' => 'Animation',
                'parent_id' => 20,
                'order' => 0,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 22,
                'type' => 0,
                'name' => '<en>Audio & Video</en><KP>音频视频</KP><CH>数据库管理</CH>',
                'desc' => 'Audio & Video',
                'parent_id' => 20,
                'order' => 1,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 23,
                'type' => 0,
                'name' => '<en>Graphic Design</en><KP>그라픽 디자인</KP><CH>平面设计</CH>',
                'desc' => 'Graphic Design',
                'parent_id' => 20,
                'order' => 2,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 24,
                'type' => 0,
                'name' => '<en>Logo Design & Branding</en><KP>로고 디자인 및 브랜딩</KP><CH>标志设计和品牌</CH>',
                'desc' => 'Logo Design & Branding',
                'parent_id' => 20,
                'order' => 3,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 25,
                'type' => 0,
                'name' => '<en>Photography</en><KP>사진</KP><CH>摄影</CH>',
                'desc' => 'Photography',
                'parent_id' => 20,
                'order' => 4,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 26,
                'type' => 0,
                'name' => '<en>Writing & Translation</en><ch>写作和翻译</ch><kp>글쓰기 및 번역</kp>',
                'desc' => 'Writing & Translation',
                'parent_id' => 0,
                'order' => 3,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 27,
                'type' => 0,
                'name' => '<en>Article</en><KP>기사</KP><CH>文章</CH>',
                'desc' => 'Article',
                'parent_id' => 26,
                'order' => 0,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 28,
                'type' => 0,
                'name' => '<en>Copywriting & Proofreading</en><KP>저작권 및 교정</KP><CH>文案和校对</CH>',
                'desc' => 'Copywriting & Proofreading',
                'parent_id' => 26,
                'order' => 1,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 29,
                'type' => 0,
                'name' => '<en>Creative Writing</en><KP>창작</KP><CH>创意写作</CH>',
                'desc' => 'Creative Writing',
                'parent_id' => 26,
                'order' => 2,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 30,
                'type' => 0,
                'name' => '<en>CV/Cover Letter</en><KP>리력서</KP><CH>简历/求职信</CH>',
                'desc' => 'CV/Cover Letter',
                'parent_id' => 26,
                'order' => 3,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 31,
                'type' => 0,
                'name' => '<en>Technical writing</en><KP>기술적인 글쓰기</KP><CH>技术写作</CH>',
                'desc' => 'Technical writing',
                'parent_id' => 26,
                'order' => 4,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 32,
                'type' => 0,
                'name' => '<en>Translation</en><ch>翻译</ch><kp>번역</kp>',
                'desc' => 'Translation',
                'parent_id' => 26,
                'order' => 5,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 33,
                'type' => 0,
                'name' => '<en>Other</en><KP>기타</KP><CH>其他</CH>',
                'desc' => 'Other',
                'parent_id' => 26,
                'order' => 6,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 34,
                'type' => 0,
                'name' => '<en>Admin Support</en><KP>관리지원</KP><CH>管理支持</CH>',
                'desc' => 'Admin Support',
                'parent_id' => 0,
                'order' => 4,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 35,
                'type' => 0,
                'name' => '<en>Data Entry</en><ch>数据输入</ch><kp>자료입력</kp>',
                'desc' => 'Data Entry',
                'parent_id' => 34,
                'order' => 0,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 36,
                'type' => 0,
                'name' => '<en>Virtual Assistant</en><KP>가상조수</KP><CH>虚拟助手</CH>',
                'desc' => 'Virtual Assistant',
                'parent_id' => 34,
                'order' => 1,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 37,
                'type' => 0,
                'name' => '<en>Project Management</en><KP>프로젝트 관리</KP><CH>项目管理</CH>',
                'desc' => 'Project Management',
                'parent_id' => 34,
                'order' => 2,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 38,
                'type' => 0,
                'name' => '<en>Other</en><KP>기타</KP><CH>其他</CH>',
                'desc' => 'Other',
                'parent_id' => 34,
                'order' => 3,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 41,
                'type' => 0,
                'name' => '<en>Sales & Marketing</en><KP>판매 및 마케팅</KP><CH>销售与市场营销</CH>',
                'desc' => 'Sales & Marketing',
                'parent_id' => 0,
                'order' => 5,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 42,
                'type' => 0,
                'name' => '<en>Email marketing</en><KP>이메일 마케팅</KP><CH>电子邮件营销</CH>',
                'desc' => 'Email marketing',
                'parent_id' => 41,
                'order' => 0,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 43,
                'type' => 0,
                'name' => '<en>Marketing Strategy</en><KP>마케팅 전략</KP><CH>市场策略</CH>',
                'desc' => 'Marketing Strategy',
                'parent_id' => 41,
                'order' => 1,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 44,
                'type' => 0,
                'name' => '<en>SEM / Adwords / PPC</en><KP>SEM / Adwords / PPC</KP><CH>SEM / Adwords / PPC</CH>',
                'desc' => 'SEM / Adwords / PPC',
                'parent_id' => 41,
                'order' => 2,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 45,
                'type' => 0,
                'name' => '<en>SEO - Search Engine Optimization</en><KP>SEO - 검색엔진 최적화</KP><CH>SEO - 搜索引擎优化</CH>',
                'desc' => 'SEO - Search Engine Optimization',
                'parent_id' => 41,
                'order' => 3,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 46,
                'type' => 0,
                'name' => '<en>SMM - Social Media Marketing</en><KP>SMM - 소셜 미디아 마케팅</KP><CH>SMM - 社交媒体市场营销</CH>',
                'desc' => 'SMM - Social Media Marketing',
                'parent_id' => 41,
                'order' => 4,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 47,
                'type' => 0,
                'name' => '<en>Other</en><KP>기타</KP><CH>其他</CH>',
                'desc' => 'Other',
                'parent_id' => 41,
                'order' => 5,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 54,
                'type' => 0,
                'name' => '<en>Engineering & Architecture</en><KP>엔지니어링 및 구조설계</KP><CH>工程与建筑</CH>',
                'desc' => 'Engineering & Architecture',
                'parent_id' => 0,
                'order' => 6,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 60,
                'type' => 0,
                'name' => '<en>Accounting & Consulting</en><KP>회계 및 컨설팅</KP><CH>会计与咨询</CH>',
                'desc' => 'Accounting & Consulting',
                'parent_id' => 0,
                'order' => 7,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 91,
                'type' => 4,
                'name' => '<en>Basic English</en><ch>基本的英语</ch><kp>기초영어</kp>',
                'desc' => NULL,
                'parent_id' => 0,
                'order' => 1,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 92,
                'type' => 4,
                'name' => '<en>Conversational English</en><ch>会话英语</ch><kp>회화영어</kp>',
                'desc' => NULL,
                'parent_id' => 0,
                'order' => 2,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 93,
                'type' => 4,
                'name' => '<en>Fluent English</en><ch>流利的英语</ch><kp>줄줄영어</kp>',
                'desc' => NULL,
                'parent_id' => 0,
                'order' => 3,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 94,
                'type' => 4,
                'name' => '<en>Native or Bilingual English</en><ch>母语或双语英语</ch><kp>원어민영어</kp>',
                'desc' => NULL,
                'parent_id' => 0,
                'order' => 4,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 97,
                'type' => 0,
                'name' => '<en>Other</en><KP>기타</KP><CH>其他</CH>',
                'desc' => 'Other',
                'parent_id' => 20,
                'order' => 10000,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}