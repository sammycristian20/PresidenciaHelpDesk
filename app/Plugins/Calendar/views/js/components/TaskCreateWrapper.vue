<template>
<div>
    <Alert componentName="taskCreate"/>

    <FaveoBox :title="(editPage) ? lang('task_edit'): lang('task_create')">

        <TaskCreate />
    </FaveoBox>
</div>
</template>

<script>
import TaskCreate from './TaskCreateEdit';
import Alert from "components/MiniComponent/Alert";
import FaveoBox from 'components/MiniComponent/FaveoBox';
export default {
    props: {
        editPage : { type : Boolean , default : false }
    },
    components : {TaskCreate,Alert,FaveoBox}
}
</script>