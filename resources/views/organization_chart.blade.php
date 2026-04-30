@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto my-8">
    <h1 class="text-3xl font-bold mb-8 text-center">Organization Chart</h1>
    <div id="orgChart" class="flex flex-col items-center"></div>
</div>

<style>
    .org-node {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }
    
    .node-content {
        background-color: white;
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        width: 200px;
    }
    
    .profile-image {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid #7fbfb6;
        margin: -40px auto 1rem;
        overflow: hidden;
        background: #f0f0f0;
    }
    
    .department-box {
        background-color: #e8f5e9;
        border-radius: 4px;
        padding: 0.5rem;
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: #2e7d32;
    }
    
    .children-container {
        display: flex;
        justify-content: center;
        gap: 2rem;
        position: relative;
        padding-top: 2rem;
    }
    
    .children-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        width: 2px;
        height: 2rem;
        background-color: #7fbfb6;
    }
    
    .children-container > .org-node::before {
        content: '';
        position: absolute;
        top: -2rem;
        left: 50%;
        width: 2px;
        height: 2rem;
        background-color: #7fbfb6;
    }
    
    .children-container > .org-node:not(:first-child)::after {
        content: '';
        position: absolute;
        top: -2rem;
        left: -1rem;
        width: calc(100% + 2rem);
        height: 2px;
        background-color: #7fbfb6;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const employees = [
            @foreach($employees as $employee)
            {
                id: {{ $employee->employee_id }},
                pid: {{ $employee->manager_id ?? 'null' }},
                name: "{{ $employee->employee_name }}",
                designation: "{{ $employee->employee_designation }}",
                department: "{{ $employee->employee_department }}",
                email: "{{ $employee->employee_email }}",
                image: "{{ $employee->profile_image ?? 'default-profile.jpg' }}"
            },
            @endforeach
        ];

        function createOrgChart(data) {
            const rootNodes = data.filter(node => !node.pid || node.pid === null);
            const orgChartDiv = document.getElementById('orgChart');
            orgChartDiv.innerHTML = '';
            
            rootNodes.forEach(node => {
                const nodeElement = createNodeElement(node, data);
                orgChartDiv.appendChild(nodeElement);
            });
        }

        function createNodeElement(node, data) {
            const nodeDiv = document.createElement('div');
            nodeDiv.classList.add('org-node');

            const contentDiv = document.createElement('div');
            contentDiv.classList.add('node-content');

            const imageDiv = document.createElement('div');
            imageDiv.classList.add('profile-image');
            const img = document.createElement('img');
            img.src = node.image;
            img.alt = node.name;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            imageDiv.appendChild(img);

            const nameDiv = document.createElement('div');
            nameDiv.classList.add('font-bold', 'text-lg', 'mb-1');
            nameDiv.textContent = node.name;

            const designationDiv = document.createElement('div');
            designationDiv.classList.add('text-gray-600', 'text-sm', 'mb-2');
            designationDiv.textContent = node.designation;

            const departmentDiv = document.createElement('div');
            departmentDiv.classList.add('department-box');
            departmentDiv.textContent = node.department;

            contentDiv.appendChild(imageDiv);
            contentDiv.appendChild(nameDiv);
            contentDiv.appendChild(designationDiv);
            contentDiv.appendChild(departmentDiv);
            nodeDiv.appendChild(contentDiv);

            const childNodes = data.filter(child => child.pid === node.id);
            if (childNodes.length > 0) {
                const childrenDiv = document.createElement('div');
                childrenDiv.classList.add('children-container');
                childNodes.forEach(childNode => {
                    const childElement = createNodeElement(childNode, data);
                    childrenDiv.appendChild(childElement);
                });
                nodeDiv.appendChild(childrenDiv);
            }

            return nodeDiv;
        }

        createOrgChart(employees);
    });
</script>
@endsection